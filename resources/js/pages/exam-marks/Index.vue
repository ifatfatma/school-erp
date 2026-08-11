<script setup>
import { reactive, onMounted, watch,ref } from "vue";
import examMarkApi from "@/api/examMark";
import { useExamMarkStore } from "@/stores/examMark";
import { useAcademicSessionStore } from "@/stores/academicSession";
import { useExamStore } from "@/stores/exam";
import { useClassStore } from "@/stores/class";
import { useSectionStore } from "@/stores/section";
import { useExamSubjectStore } from "@/stores/examSubject";
import resultGradeApi from "@/api/resultGrade";
const grades = ref([]);
const markStore = useExamMarkStore();

const sessionStore = useAcademicSessionStore();
const examStore = useExamStore();
const classStore = useClassStore();
const sectionStore = useSectionStore();
const subjectStore = useExamSubjectStore();

const filter = reactive({
    academic_session_id: "",
    exam_id: "",
    class_id: "",
    section_id: "",
    subject_id: "",
});

onMounted(async () => {

    await Promise.all([
        sessionStore.active(),
        examStore.active(),
        classStore.active(),
        loadGrades(),
    ]);

});

watch(
    () => filter.class_id,
    async (classId) => {

        filter.section_id = "";

        if (!classId) {

            sectionStore.items = [];

            return;
        }

        await sectionStore.load({
            class_id: classId,
            status: 1,
            per_page: 1000
        });

    }
);

watch(
    () => [filter.exam_id, filter.class_id],
    async ([examId, classId]) => {

        filter.subject_id = "";

        subjectStore.items = [];

        if (!examId || !classId) {
            return;
        }

        await subjectStore.byExamAndClass(
            examId,
            classId
        );

    }
);

async function loadGrades() {

    try {

        const { data } =
            await resultGradeApi.active();

        console.log(
            "Result Grades:",
            data
        );

        grades.value =
            data.data ?? [];

    } catch (error) {

        console.error(
            "Failed to load result grades:",
            error
        );

        grades.value = [];
    }
}

async function loadStudents() {

    if (
        !filter.exam_id ||
        !filter.class_id ||
        !filter.section_id ||
        !filter.subject_id
    ) {

        alert(
            "Please select Exam, Class, Section and Subject."
        );

        return;
    }

    console.log("Marks filters:", filter);

    await markStore.load(filter);

}




function calculate(student) {

    // Calculate total
    student.total_marks =
        Number(student.obtained_theory || 0) +
        Number(student.obtained_practical || 0) +
        Number(student.obtained_viva || 0) +
        Number(student.obtained_project || 0);


    // Get full marks from schedule
    const fullMarks = Number(
        markStore.schedule?.exam_subject?.full_marks
        ??
        markStore.schedule?.full_marks
        ??
        0
    );


    // Calculate percentage
    student.percentage =
        fullMarks > 0
            ? Number(
                (
                    student.total_marks /
                    fullMarks *
                    100
                ).toFixed(2)
            )
            : 0;


    // Find grade
    const grade = grades.value.find(g => {

        const min =
            Number(g.min_percentage);

        const max =
            Number(g.max_percentage);

        return (
            student.percentage >= min &&
            student.percentage <= max
        );

    });


    // Set grade
    student.grade =
        grade
            ? grade.name
            : "-";


    // Set grade point
    student.grade_point =
        grade
            ? Number(grade.grade_point)
            : 0;


    // Set result
    student.result =
        grade
            ? (
                grade.is_pass
                    ? "Pass"
                    : "Fail"
            )
            : "-";


    console.log(
        "Grade calculation:",
        {
            total: student.total_marks,
            fullMarks,
            percentage: student.percentage,
            grade,
            result: student.result,
        }
    );
}
async function saveMarks() {

    try {

        // 1. Get the exam schedule first
        const response = await examMarkApi.load({
            academic_session_id: filter.academic_session_id,
            exam_id: filter.exam_id,
            class_id: filter.class_id,
            section_id: filter.section_id,
            subject_id: filter.subject_id,
        });

        console.log(
            "Schedule response before save:",
            response.data
        );

        const schedule =
            response.data?.data?.schedule;

        if (!schedule?.id) {

            alert(
                "Exam schedule not found for the selected Exam, Class, Section and Subject."
            );

            return;
        }

        const scheduleId =
            Number(schedule.id);

        console.log(
            "Exam Schedule ID:",
            scheduleId
        );


        // 2. Get students from the store
        if (!markStore.students?.length) {

            alert("No students found.");

            return;
        }


        // 3. Prepare marks
        const marks =
            markStore.students.map(student => ({

                student_id:
                    Number(student.id),

                theory_marks:
                    Number(
                        student.obtained_theory || 0
                    ),

                practical_marks:
                    Number(
                        student.obtained_practical || 0
                    ),

                viva_marks:
                    Number(
                        student.obtained_viva || 0
                    ),

                project_marks:
                    Number(
                        student.obtained_project || 0
                    ),

                total_marks:
                    Number(
                        student.total_marks || 0
                    ),

                percentage:
                    Number(
                        student.percentage || 0
                    ),

                grade:
                    student.grade || null,

                grade_point:
                    Number(
                        student.grade_point || 0
                    ),

                result:
                    student.result || null,

                attendance:
                    student.attendance || "Present",

                remarks:
                    student.remarks || null,

            }));


        // 4. FINAL PAYLOAD
        const payload = {

            exam_schedule_id:
                scheduleId,

            marks:
                marks,

        };


        console.log(
            "FINAL SAVE PAYLOAD:",
            JSON.stringify(
                payload,
                null,
                2
            )
        );


        // 5. Save
        await examMarkApi.save(payload);

        alert(
            "Marks saved successfully."
        );


    } catch (error) {

        console.error(
            "Save marks error:",
            error.response?.data || error
        );

    }
}
</script>

<template>

<div class="container-fluid">

<div class="card">

<div class="card-header">

<h5>Marks Entry</h5>

</div>

<div class="card-body">

<div class="row g-3 mb-3">

<div class="col-md-2">

<select
class="form-select"
v-model="filter.academic_session_id">

<option value="">Academic Session</option>

<option
v-for="item in sessionStore.sessions"
:key="item.id"
:value="item.id">

{{ item.name }}

</option>

</select>

</div>

<div class="col-md-2">

<select
class="form-select"
v-model="filter.exam_id">

<option value="">Exam</option>

<option
v-for="item in examStore.items"
:key="item.id"
:value="item.id">

{{ item.name }}

</option>

</select>

</div>

<div class="col-md-2">

<select
class="form-select"
v-model="filter.class_id">

<option value="">Class</option>

<option
v-for="item in classStore.items"
:key="item.id"
:value="item.id">

{{ item.name }}

</option>

</select>

</div>

<div class="col-md-2">

<select
class="form-select"
v-model="filter.section_id">

<option value="">Section</option>

<option
v-for="item in sectionStore.items"
:key="item.id"
:value="item.id">

{{ item.name }}

</option>

</select>

</div>

<div class="col-md-2">

<select
class="form-select"
v-model="filter.subject_id">
<option value="">Subject</option>
<option
v-for="item in subjectStore.items"
:key="item.id"
:value="item.id">
{{ item.subject.name }}
</option>
</select>
</div>
<div class="col-md-2">
<button
class="btn btn-primary w-100"
@click="loadStudents">

Load Students

</button>

</div>

</div>

<div class="table-responsive">

<table class="table table-bordered table-striped">

<thead>

<tr>

<th>Roll</th>

<th>Admission</th>

<th>Student</th>

<th>Theory</th>

<th>Practical</th>

<th>Viva</th>

<th>Project</th>

<th>Total</th>

<th>Grade</th>

<th>Result</th>

</tr>

</thead>

<tbody>

<tr
v-for="student in markStore.students"
:key="student.student_id">

<td>{{ student.roll_no }}</td>

<td>{{ student.admission_no }}</td>

<td>{{ student.first_name }}</td>

<td>

<input
type="number"
class="form-control"
v-model.number="student.obtained_theory"
@input="calculate(student)">

</td>

<td>

<input
type="number"
class="form-control"
v-model.number="student.obtained_practical"
@input="calculate(student)">

</td>

<td>

<input
type="number"
class="form-control"
v-model.number="student.obtained_viva"
@input="calculate(student)">

</td>

<td>

<input
type="number"
class="form-control"
v-model.number="student.obtained_project"
@input="calculate(student)">

</td>

<td>

{{ student.total_marks }}

</td>

<td>

{{ student.grade }}

</td>

<td>

{{ student.result }}

</td>

</tr>

<tr v-if="!markStore.students.length">

<td colspan="10" class="text-center">

No students found.

</td>

</tr>

</tbody>

</table>

</div>

<div class="mt-3">

<button
class="btn btn-success"
@click="saveMarks"
:disabled="markStore.saving">

Save Marks

</button>

</div>

</div>

</div>

</div>

</template>