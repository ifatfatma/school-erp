<template>
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3>Schools</h3>
      <button class="btn btn-primary" @click="openCreateModal">+ Add School</button>
    </div>

    <!-- Search -->
    <div class="mb-3">
      <input type="text" class="form-control" v-model="search" placeholder="Search by name or code..." @input="searchSchools" />
    </div>

    <!-- Loading -->
    <div v-if="store.loading" class="text-center py-5">
      <div class="spinner-border text-primary" />
    </div>

    <!-- Error -->
    <div v-else-if="store.error" class="alert alert-danger">
      {{ store.error }}
    </div>

    <!-- Table -->
    <div v-else>
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Code</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="school in store.schools" :key="school.id">
            <td>{{ school.id }}</td>
            <td>{{ school.name }}</td>
            <td>{{ school.code }}</td>
            <td>{{ school.email || '-' }}</td>
            <td>{{ school.phone || '-' }}</td>
            <td>
              <span :class="school.status ? 'badge bg-success' : 'badge bg-secondary'">
                {{ school.status ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td>
              <button class="btn btn-sm btn-info me-1" @click="openEditModal(school)">Edit</button>
              <button class="btn btn-sm btn-danger" @click="confirmDelete(school.id)">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <nav>
        <ul class="pagination">
          <li class="page-item" :class="{ disabled: store.pagination.current_page <= 1 }">
            <a class="page-link" @click.prevent="changePage(store.pagination.current_page - 1)">Prev</a>
          </li>
          <li class="page-item active"><span class="page-link">{{ store.pagination.current_page }}</span></li>
          <li class="page-item" :class="{ disabled: store.pagination.current_page >= store.pagination.last_page }">
            <a class="page-link" @click.prevent="changePage(store.pagination.current_page + 1)">Next</a>
          </li>
        </ul>
      </nav>
    </div>

    <!-- Modal for Create/Edit -->
    <SchoolForm ref="formModal" @saved="onSaved" />
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useSchoolStore } from '@/stores/school';
import SchoolForm from '@/pages/school/SchoolForm.vue';

const store = useSchoolStore();
const search = ref('');
const formModal = ref(null);

const loadSchools = () => {
  store.fetchSchools({ page: store.pagination.current_page, search: search.value });
};

onMounted(loadSchools);

const searchSchools = () => {
  store.pagination.current_page = 1;
  loadSchools();
};

const changePage = (page) => {
  if (page >= 1 && page <= store.pagination.last_page) {
    store.pagination.current_page = page;
    loadSchools();
  }
};

const openCreateModal = () => {
  formModal.value.open();
};

const openEditModal = (school) => {
  formModal.value.open(school);
};

const confirmDelete = (id) => {
  if (confirm('Are you sure you want to delete this school?')) {
    store.deleteSchool(id);
  }
};

const onSaved = () => {
  loadSchools();
};
</script>