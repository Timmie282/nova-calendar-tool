<template>
  <modal @modal-close="handleClose" classWhitelist="flatpickr-calendar">
    <form
        @submit.prevent="handleSave"
        slot-scope="props"
        class="bg-white rounded-lg shadow-lg overflow-hidden"
        style="width: 460px"
    >
      <slot :uppercaseMode="uppercaseMode" :mode="mode">
        <div class="p-8">
          <heading v-if="!currentEvent" :level="2" class="mb-6">{{ __('Create Event') }}</heading>
          <heading v-if="currentEvent" :level="2" class="mb-6">{{ __('Edit Event') }}</heading>
          <p>{{ currentData }}</p>
          <div class="border-b border-40 pb-4">
            <label for="project_id" class="mb-2 text-80 leading-tight">Project:</label>
            <select v-model="project_id" name="project_id" class="w-full form-control form-input form-input-bordered">
              <option v-for="project in projects" :value=" project.pro_id " :selected="project.project_id === currentData.project_id">
                {{ project.name }}
              </option>
            </select>
          </div>
          <div class="border-b border-40 pb-4">
            <label for="estate_id" class="mb-2 text-80 leading-tight">Estates:</label>
            <select v-model="estate_id" name="estate_id" class="w-full form-control form-input form-input-bordered">
              <option v-for="estate in estates" :value=" estate.est_id " v-bind:selected="estate.est_id === currentData.estate_id">
                {{ estate.address }}
              </option>
            </select>
          </div>
          <div class="border-b border-40 pb-4">
            <label for="title" class="mb-2 text-80 leading-tight">Title:</label>
            <input v-model="title" name="title" class="w-full form-control form-input form-input-bordered"/>
          </div>
          <div class="border-b border-40 pb-4">
            <label for="description" class="mb-2 text-80 leading-tight">Description:</label>
            <textarea v-model="description" name="description" class="w-full form-control form-input form-input-bordered">{{ currentData.description }}</textarea>
          </div>
          <div class="border-b border-40 py-4">
            <label for="start" class="mb-2 text-80 leading-tight">Start:</label>
            <date-time-picker @change="changeStart" v-model="start" name="start" class="w-full form-control form-input form-input-bordered" autocomplete="off"/>
          </div>
          <div class="border-b border-40 py-4">
            <label for="end" class="mb-2 text-80">End:</label>
            <date-time-picker @change="changeEnd" v-model="end" name="end" class="w-full form-control form-input form-input-bordered" autocomplete="off"/>
          </div>
        </div>
      </slot>

      <div class="btn-wrapper bg-30 px-6 py-3">
        <button v-if="currentEvent" @click.prevent="handleDelete" type="button" class="btn btn-default btn-danger delete-event">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" aria-labelledby="delete" role="presentation" class="fill-current">
            <path fill-rule="nonzero" d="M6 4V2a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2h5a1 1 0 0 1 0 2h-1v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6H1a1 1 0 1 1 0-2h5zM4 6v12h12V6H4zm8-2V2H8v2h4zM8 8a1 1 0 0 1 1 1v6a1 1 0 0 1-2 0V9a1 1 0 0 1 1-1zm4 0a1 1 0 0 1 1 1v6a1 1 0 0 1-2 0V9a1 1 0 0 1 1-1z"></path>
          </svg>
        </button>
        <button @click.prevent="handleClose" type="button" class="btn text-80 font-normal h-9 px-3 btn-link">
          {{ __('Cancel') }}
        </button>
        <button @click.prevent="handleSave" ref="saveButton" type="submit" class="btn btn-default btn-primary ml-3">
          {{ __('Save') }}
        </button>
      </div>
    </form>
  </modal>
</template>

<script>
export default {
  el: '#app',
  name: 'EventModal',
  props: ['currentEvent', 'currentDate'],
  data() {
    return {
      projects: [],
      estates: [],
      currentData: [],
      project_id: this.currentEvent !== null ? this.currentEvent.event.project_id : '',
      estate_id: this.currentEvent !== null ? this.currentEvent.event.estate_id : '',
      title: this.currentEvent !== null ? this.currentEvent.event.title : '',
      description: this.currentEvent !== null ? this.currentEvent.event.description : '',
      start: moment(this.currentEvent !== null ? this.currentEvent.event.start : this.currentDate.date).format('YYYY-MM-DD HH:mm:ss'),
      end: this.currentEvent !== null ? moment(this.currentEvent.event.end).format('YYYY-MM-DD HH:mm:ss') : moment(this.currentDate.date).add(1, 'hour').format('YYYY-MM-DD HH:mm:ss')
    }
  },
  methods: {
    changeStart(value) {
      this.start = value;
    },
    changeEnd(value) {
      this.end = value;
    },
    handleClose() {
      this.$emit('close');
    },
    handleDelete() {
      Nova.request()
          .delete('/nova-vendor/nova-calendar-tool/events/' + this.currentEvent.event.id + '/destroy')
          .then(response => {
            if (response.data.success) {
              this.$toasted.show('Event has been deleted', {type: 'success'});
              this.$emit('close');
              this.$emit('refreshEvents');
            }
          })
          .catch(response => this.$toasted.show('Something went wrong', {type: 'error'}));
    },
    handleSave() {
      let data = {
        project_id: this.project_id,
        estate_id: this.estate_id,
        title: this.title,
        description: this.description,
        start: this.start,
        end: this.end,
      };

      if (this.currentEvent === null) {
        Nova.request()
            .post('/nova-vendor/nova-calendar-tool/events/store', data)
            .then(response => {
              if (response.data.success) {
                this.$toasted.show('Event has been created', {type: 'success'});
                this.$emit('close');
                this.$emit('refreshEvents');
              } else if (response.data.error === true) {
                this.$toasted.show(response.data.message, {type: 'error'});
              }
            })
            .catch(response => this.$toasted.show('Something went wrong', {type: 'error'}));
      } else if (this.currentEvent !== null) {
        Nova.request()
            .put('/nova-vendor/nova-calendar-tool/events/' + this.currentEvent.event.id + '/update', data)
            .then(response => {
              if (response.data.success) {
                this.$toasted.show('Event has been updated', {type: 'success'});
                this.$emit('close');
                this.$emit('refreshEvents');
              } else if (response.data.error === true) {
                this.$toasted.show(response.data.message, {type: 'error'});
              }
            })
            .catch(response => this.$toasted.show('Something went wrong', {type: 'error'}));
      }
    },
    selectedEst(id) {
      if (estate.est_id === id){
        return 'selected'
      } else {
        return ''
      }
    },
  },
  created() {
    axios.get('/nova-vendor/nova-calendar-tool/events/projects')
        .then(response => this.projects = response.data);

    axios.get('/nova-vendor/nova-calendar-tool/events/estates')
        .then(response => this.estates = response.data);

    if (this.currentEvent !== null) {
      axios.get('/nova-vendor/nova-calendar-tool/events/' + this.currentEvent.event.id)
          .then(response => this.currentData = response.data);
    } else {
      this.currentData = [];
    }
  },
}
</script>

<style scoped>
label {
  display: block;
}

.btn-wrapper {
  display: flex;
  justify-content: flex-end;
}

.btn.delete-event {
  margin-right: auto;
  display: flex;
  justify-content: center;
  align-items: center;
}
</style>