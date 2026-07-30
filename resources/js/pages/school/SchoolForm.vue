<template>
  <div class="modal fade" ref="modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ isEdit ? 'Edit School' : 'Add School' }}</h5>
          <button type="button" class="btn-close" @click="close" />
        </div>
        <div class="modal-body">
          <form @submit.prevent="submit">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Name *</label>
                <input type="text" class="form-control" v-model="form.name" required />
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Code *</label>
                <input type="text" class="form-control" v-model="form.code" required />
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" v-model="form.email" />
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Phone</label>
                <input type="text" class="form-control" v-model="form.phone" />
              </div>
              <div class="col-md-12 mb-3">
                <label class="form-label">Address</label>
                <textarea class="form-control" v-model="form.address" rows="2"></textarea>
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">City</label>
                <input type="text" class="form-control" v-model="form.city" />
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">State</label>
                <input type="text" class="form-control" v-model="form.state" />
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">Country</label>
                <input type="text" class="form-control" v-model="form.country" />
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">Postal Code</label>
                <input type="text" class="form-control" v-model="form.postal_code" />
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Logo URL</label>
                <input type="text" class="form-control" v-model="form.logo" placeholder="Path or URL" />
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Status</label>
                <select class="form-select" v-model="form.status">
                  <option :value="1">Active</option>
                  <option :value="0">Inactive</option>
                </select>
              </div>
            </div>
            <div v-if="store.error" class="alert alert-danger">{{ store.error }}</div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="close">Cancel</button>
              <button type="submit" class="btn btn-primary" :disabled="store.loading">
                {{ store.loading ? 'Saving...' : 'Save' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, nextTick } from 'vue';
import { useSchoolStore } from '@/stores/school';
import { Modal } from 'bootstrap';

const store = useSchoolStore();
const modal = ref(null);
let bsModal = null;

const isEdit = ref(false);
const currentId = ref(null);

const form = reactive({
  name: '',
  code: '',
  email: '',
  phone: '',
  address: '',
  city: '',
  state: '',
  country: '',
  postal_code: '',
  logo: '',
  status: 1,
});

const emit = defineEmits(['saved']);

const open = (school = null) => {
  isEdit.value = !!school;
  if (school) {
    currentId.value = school.id;
    Object.assign(form, { ...school, status: school.status ? 1 : 0 });
  } else {
    currentId.value = null;
    Object.assign(form, {
      name: '', code: '', email: '', phone: '', address: '',
      city: '', state: '', country: '', postal_code: '', logo: '', status: 1
    });
  }
  store.error = null;
  if (!bsModal) {
    bsModal = new Modal(modal.value);
  }
  bsModal.show();
};

const close = () => {
  bsModal.hide();
};

const submit = async () => {
  try {
    if (isEdit.value) {
      await store.updateSchool(currentId.value, form);
    } else {
      await store.createSchool(form);
    }
    close();
    emit('saved');
  } catch (err) {
    // error is already in store
  }
};

defineExpose({ open });
</script>