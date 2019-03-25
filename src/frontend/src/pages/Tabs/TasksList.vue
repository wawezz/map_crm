<template>
  <div>
    <base-button
      v-if="tasks.length === 0"
      data-toggle="modal"
      data-target="taskAdd"
      type="button"
      class="btn btn-primary btn-block"
      @click="taskAddModalVisible = true"
    >
      <i class="tim-icons icon-simple-add"></i> Add task
    </base-button>
    <div class="taskBlock row">
      <div class="col-12">
        <div
          v-for="task in tasks"
          :key="task.id"
          :class="Date.now() < new Date(task.eventDate.date)?'taskBlock__item active':'taskBlock__item'"
        >
          Task
          <b>N {{task.id}}</b> created by:
          <router-link :to="'/profile/'  + task.createdBy">{{task.createdByName}}</router-link>
          <b>{{task.eventDate.date | dateTime}}</b> in
          <router-link :to="options.path  + task.elementId">{{options.name}}</router-link>
          <br>
          Type: {{task.type=='connect'?'Connect to client':task.type=='meeting'?'Meeting':'Other'}}
          <br>
          {{task.comment}}
          <br>
          <span v-if="userId == task.responsible || userId == task.createdBy" class="taskActions">
            <a
              href="#"
              @click.prevent="closeTaskHandler(task.id)"
              alt="default"
              data-toggle="modal"
              data-target="taskClose"
            >Close</a>
            <a
              href="#"
              @click.prevent="removeTaskHandler(task.id)"
              alt="default"
              data-toggle="modal"
              data-target="taskRemove"
            >Remove</a>
          </span>
        </div>
      </div>
    </div>
    <modal :show.sync="taskAddModalVisible" id="taskAdd" :centered="false" :show-close="true">
      <h4 class="black">Add task</h4>
      <card>
        <div class="row">
          <div class="col-12">
            <label>Responsible</label>
            <select class="form-control form-control-line" v-model="tasksForm.responsible">
              <optgroup v-for="group in managerGroups" :key="group.id" :label="group.name">
                <option
                  v-for="manager in group.users"
                  :key="manager.id"
                  :value="manager.id"
                >{{manager.name}}</option>
              </optgroup>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="col-6">
            <label>Type</label>
            <select class="form-control form-control-line" v-model="tasksForm.type">
              <option
                v-for="taskType in $store.state.taskTypes"
                :key="taskType.value"
                :value="taskType.value"
              >{{taskType.title}}</option>
            </select>
          </div>
          <div class="col-6">
            <label>Date</label>
            <datepicker v-model="tasksForm.eventDate" :format="format"></datepicker>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <base-input>
              <label>Comment</label>
              <textarea rows="4" class="form-control" v-model="tasksForm.comment"></textarea>
            </base-input>
          </div>
        </div>
        <base-button
          slot="footer"
          type="primary"
          class="text-center"
          fill
          @click.prevent="addTask(true)"
        >Add task</base-button>
      </card>
    </modal>
    <modal
      :show.sync="taskCloseModalVisible"
      id="taskClose"
      :centered="false"
      :show-close="true"
      class="text-center"
    >
      <h4 class="black">
        Close task
        <b>N {{currentTask.id}}</b>
      </h4>
      <card>
        <div class="row">
          <div class="col-12">
            <base-input>
              <label>Task result</label>
              <textarea rows="4" class="form-control" v-model="currentTask.result"></textarea>
            </base-input>
          </div>
        </div>
        <base-button
          slot="footer"
          type="primary"
          class="text-center"
          fill
          @click.prevent="closeTask()"
        >Close task</base-button>
      </card>
    </modal>
    <modal
      :show.sync="taskRemoveModalVisible"
      id="taskRemove"
      :centered="false"
      :show-close="true"
      class="text-center"
    >
      <h4 class="black">
        Remove task?
        <b>N {{currentTask.id}}</b>
      </h4>
      <button class="btn btn-default" @click="taskRemoveModalVisible = false">No</button>
      <button class="btn btn-danger" @click.prevent="removeTask()">Yes</button>
    </modal>
  </div>
</template>

<script>
import { Modal } from "@/components";
import Datepicker from "vuejs-datepicker";
export default {
  components: {
    Modal,
    Datepicker
  },
  props: {
    tasks: {
      type: [Object, Array]
    },
    managerGroups: {
      type: [Object, Array]
    },
    currentTask: {
      type: Object
    },
    tasksForm: {
      type: Object,
      default: () => {
        return {};
      }
    },
    format: {
      type: String
    },
    options: {
      type: Object,
      default: () => {
        return { path: "", name: "" };
      }
    },
    userId: { type: String },
    getTaskData: { type: Function },
    addTask: { type: Function },
    closeTask: { type: Function },
    removeTask: { type: Function }
  },
  data() {
    return {
      taskAddModalVisible: false,
      taskCloseModalVisible: false,
      taskRemoveModalVisible: false
    };
  },
  methods: {
    closeTaskHandler(taskId) {
      this.getTaskData(taskId);
      this.taskCloseModalVisible = true;
    },
    removeTaskHandler(taskId) {
      this.getTaskData(taskId);
      this.taskRemoveModalVisible = true;
    }
  }
};
</script>
<style>
</style>