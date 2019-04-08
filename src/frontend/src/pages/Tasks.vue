<template>
  <div v-if="user">
    <div class="row">
      <div class="col-sm-12 mb-3">
        <base-button
          data-toggle="modal"
          data-target="userAdd"
          type="button"
          class="btn btn-primary"
          @click="taskAddModalVisible = true"
        >
          <i class="tim-icons icon-simple-add"></i> Add task
        </base-button>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <base-alert v-if="listError" type="danger">{{listError}}</base-alert>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <card title="Tasks" :loading="tasksLoading">
          <tabs :options="{ useUrlFragment: false }">
            <tab name="Current">
              <div class="taskBlock row">
                <div
                  v-for="task in tasks"
                  :key="task.id"
                  v-if="Date.now() > new Date(task.eventDate.date)"
                  class="col-6"
                >
                  <div class="taskBlock__item">
                    Task
                    <b>N {{task.id}}</b> created by:
                    <router-link :to="'/profile/'  + task.createdBy">{{task.createdByName}}</router-link>
                    <b>{{task.eventDate.date | dateTime}}</b> in
                    <router-link
                      v-if="task.elementType == $store.state.elementTypes.ELEMENT_TYPE_CLIENT.id"
                      :to="'/client/'  + task.elementId"
                    >Client</router-link>
                    <router-link
                      v-if="task.elementType == $store.state.elementTypes.ELEMENT_TYPE_LEAD.id"
                      :to="'/lead/'  + task.elementId"
                    >Lead</router-link>
                    <router-link
                      v-if="task.elementType == $store.state.elementTypes.ELEMENT_TYPE_PLACE_LEAD.id"
                      :to="'/place-lead/'  + task.elementId"
                    >Place lead</router-link>
                    <br>
                    {{task.comment}}
                    <br>
                    Type: {{task.type=='connect'?'Connect to client':task.type=='meeting'?'Meeting':'Other'}}
                    <br>
                    <span
                      v-if="user.id == task.responsible || user.id == task.createdBy"
                      class="taskActions"
                    >
                      <a
                        href="#"
                        @click.prevent="closeTaskHandler(task.id)"
                        alt="default"
                        data-toggle="modal"
                        data-target="taskClose"
                        class="taskActions"
                      >Close</a>
                      <a
                        href="#"
                        @click.prevent="removeTaskHandler(task.id)"
                        alt="default"
                        data-toggle="modal"
                        data-target="taskRemove"
                        class="taskActions"
                      >Remove</a>
                    </span>
                  </div>
                </div>
              </div>
            </tab>
            <tab name="Scheduled">
              <div class="taskBlock row">
                <div
                  v-for="task in tasks"
                  :key="task.id"
                  v-if="Date.now() < new Date(task.eventDate.date)"
                  class="col-6"
                >
                  <div class="taskBlock__item">
                    Task
                    <b>N {{task.id}}</b> created by:
                    <router-link :to="'/profile/'  + task.createdBy">{{task.createdByName}}</router-link>
                    <b>{{task.eventDate.date | dateTime}}</b> in
                    <router-link
                      :to="task.elementType == $store.state.elementTypes.ELEMENT_TYPE_CLIENT.id?'/client/'  + task.elementId:'/lead/'  + task.elementId"
                    >{{task.elementType == $store.state.elementTypes.ELEMENT_TYPE_CLIENT.id?'Client':'Lead'}}</router-link>
                    <br>
                    Type: {{task.type=='connect'?'Connect to client':task.type=='meeting'?'Meeting':'Other'}}
                    <br>
                    {{task.comment}}
                    <span
                      v-if="user.id == task.responsible + '-' + task.responsibleSecret || user.id == task.createdBy + '-' + task.createdBySecret"
                      class="taskActions"
                    >
                      <a
                        href="#"
                        @click.prevent="closeTaskHandler(task.id)"
                        alt="default"
                        data-toggle="modal"
                        data-target="taskClose"
                        class="taskActions"
                      >Close</a>
                      <a
                        href="#"
                        @click.prevent="removeTaskHandler(task.id)"
                        alt="default"
                        data-toggle="modal"
                        data-target="taskRemove"
                        class="taskActions"
                      >Remove</a>
                    </span>
                  </div>
                </div>
              </div>
            </tab>
          </tabs>
        </card>
      </div>
    </div>
    <modal :show.sync="taskAddModalVisible" id="taskAdd" :centered="false" :show-close="true">
      <base-alert v-if="error.message" type="warning">{{error.message}}</base-alert>
      <base-alert v-if="response.active" type="success">task added</base-alert>
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
          <div class="col-12">
            <base-input label="Element" @keyup="loadElements()" v-model="tasksForm.elementName"></base-input>
          </div>
          <div class="col-12">
            <div v-if="elementsResult.length > 0 && searching" class="elementSearch">
              <div
                v-for="element in elementsResult"
                class="elementSearch__item"
                @click="selectElement(element)"
                :key="element.source+element.id"
              >{{element.name}}</div>
            </div>
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
            <datepicker v-model="tasksForm.eventDate" :format="config.date.format"></datepicker>
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
  import { BaseAlert } from "@/components";
  import { Pagination } from "@/components";
  import { Modal } from "@/components";
  import Datepicker from "vuejs-datepicker";
  import { main } from "./../mixins/main";
  import { tasks } from "./../mixins/tasks";
  import { clients } from "./../mixins/clients";
  import authGuard from "./../guards/auth.guard";

  export default {
    beforeRouteEnter: authGuard,
    components: {
      BaseAlert,
      Modal,
      Datepicker,
      Pagination
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
      },
      loadElements() {
        this.searching = true;
        this.getElementsByName(this.tasksForm.elementName);
      },
      selectElement(element) {
        this.tasksForm.elementId = element.id;
        this.tasksForm.elementType = element.source;
        this.tasksForm.elementName = element.name;
        this.searching = false;
      }
    },
    created() {
      (this.tasksFilter = { responsible: this.$route.params.id }), { id: "DESC" };
      this.managersFilter = { roleId: "!=|3" };
      this.getTasks();
      this.getManagers();
    },
    mixins: [main, tasks, clients]
  };
</script>
<style>
</style>
