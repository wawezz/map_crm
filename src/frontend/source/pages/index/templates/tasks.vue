<template>
  <div v-if="user">
    <div id="page-wrapper">
      <div class="container-fluid p-t-20">
        <div class="white-box">
          <div class="row">
            <div class="col-md-12 col-lg-12">
              {{listError}}
              <div class="manage-users" v-if="!listError">
                <div>
                  <section>
                    <div class="p-20 row">
                      <div class="col-sm-12">
                        <ul class="side-icon-text pull-right">
                          <li>
                            <div
                              alt="default"
                              data-toggle="modal"
                              data-target=".bs-taskAdd-modal-sm"
                              class="model_img img-responsive"
                            >
                              <span class="circle circle-sm bg-success di">
                                <i class="ti-plus"></i>
                              </span>
                              <span>Добавить задачу</span>
                            </div>
                          </li>
                        </ul>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-12">
                        <div class="white-box m-0 white-box-customtab">
                          <ul class="nav nav-tabs tabs customtab">
                            <li class="active tab">
                              <a href="#actual" data-toggle="tab">
                                <span>Тущие</span>
                              </a>
                            </li>
                            <li class="tab">
                              <a href="#comming" data-toggle="tab">
                                <span>Запланированные</span>
                              </a>
                            </li>
                          </ul>
                        </div>
                        <div class="white-box p-t-0 m-t-0">
                          <div class="tab-content m-t-0">
                            <div class="tab-pane active" id="actual">
                              <div class="taskBlock">
                                <div
                                  v-for="task in tasks"
                                  :key="task.id"
                                  v-if="Date.now() > new Date(task.eventDate.date)"
                                  class="taskBlock__item"
                                >Задачa
                                  <b>N {{task.id}}</b> создал:
                                  <router-link
                                    :to="'/user/'  + task.createdBy"
                                  >{{task.createdByName}}</router-link>
                                  <b>{{task.eventDate.date | dateTime}}</b> в
                                  <router-link
                                    :to="task.elementType == $store.state.elementTypes.ELEMENT_TYPE_CLIENT.id?'/client/'  + task.elementId:'/lead/'  + task.elementId"
                                  >{{task.elementType == $store.state.elementTypes.ELEMENT_TYPE_CLIENT.id?'Клиент':'Лид'}}</router-link>
                                  <br>
                                  {{task.comment}}
                                  <br>
                                  Тип: {{task.type=='connect'?'Связь с клиентом':task.type=='meeting'?'Встреча':'Другое'}}
                                  <br>
                                  <span
                                    v-if="user.id == task.responsible + '-' + task.responsibleSecret || user.id == task.createdBy + '-' + task.createdBySecret"
                                  >
                                    <a
                                      href="#"
                                      @click.prevent="getTaskData(task.id)"
                                      alt="default"
                                      data-toggle="modal"
                                      data-target=".bs-closeTask-modal-md"
                                    >Закрыть</a>
                                    <a
                                      href="#"
                                      @click.prevent="getTaskData(task.id)"
                                      alt="default"
                                      data-toggle="modal"
                                      data-target=".bs-removeTask-modal-md"
                                    >Удалить</a>
                                  </span>
                                </div>
                              </div>
                            </div>
                            <div class="tab-pane" id="comming">
                              <div class="taskBlock">
                                <div
                                  v-for="task in tasks"
                                  :key="task.id"
                                  v-if="Date.now() < new Date(task.eventDate.date)"
                                  class="taskBlock__item active"
                                >
                                  Задачa
                                  <b>N {{task.id}}</b> создал:
                                  <router-link
                                    :to="'/user/'  + task.createdBy"
                                  >{{task.createdByName}}</router-link>
                                  <b>{{task.eventDate.date | dateTime}}</b> в
                                  <router-link
                                    :to="task.elementType == $store.state.elementTypes.ELEMENT_TYPE_CLIENT.id?'/client/'  + task.elementId:'/lead/'  + task.elementId"
                                  >{{task.elementType == $store.state.elementTypes.ELEMENT_TYPE_CLIENT.id?'Клиент':'Лид'}}</router-link>
                                  <br>
                                  Тип: {{task.type=='connect'?'Связаться с клиентом':task.type=='meeting'?'Встреча':'Другое'}}
                                  <br>
                                  {{task.comment}}
                                  <span
                                    v-if="user.id == task.responsible + '-' + task.responsibleSecret || user.id == task.createdBy + '-' + task.createdBySecret"
                                  >
                                    <a
                                      href="#"
                                      @click.prevent="getTaskData(task.id)"
                                      alt="default"
                                      data-toggle="modal"
                                      data-target=".bs-closeTask-modal-md"
                                    >Закрыть</a>
                                    <a
                                      href="#"
                                      @click.prevent="getTaskData(task.id)"
                                      alt="default"
                                      data-toggle="modal"
                                      data-target=".bs-removeTask-modal-md"
                                    >Удалить</a>
                                  </span>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </section>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div
        class="modal fade bs-taskAdd-modal-sm"
        tabindex="-1"
        role="dialog"
        aria-labelledby="myLargeModalLabel"
        aria-hidden="true"
        style="display: none;"
      >
        <div class="modal-dialog modal-md">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h4 class="modal-title" id="myLargeModalLabel">Добавить задачу</h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal form-material">
                <div class="alert alert-success" v-if="response.active">задача добавлена</div>
                <div class="alert alert-danger" v-if="error.message">{{error.message}}</div>
                <div class="form-group">
                  <label class="col-sm-12">Ответственный</label>
                  <div class="col-sm-12">
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
                <div class="form-group">
                  <label class="col-sm-12">Клиент / Лид</label>
                  <div class="col-sm-12">
                    <input
                      type="text"
                      v-model="tasksForm.elementName"
                      @keyup="loadElements()"
                      class="form-control form-control-line"
                    >
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
                <div class="form-group">
                  <label class="col-sm-12">Тип</label>
                  <div class="col-sm-12">
                    <select class="form-control form-control-line" v-model="tasksForm.type">
                      <option
                        v-for="taskType in $store.state.taskTypes"
                        :key="taskType.value"
                        :value="taskType.value"
                      >{{taskType.title}}</option>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-md-12">Дата</label>
                  <div class="col-md-12">
                    <date-picker v-model="tasksForm.eventDate" :config="config.date"></date-picker>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-12">Комментарий</label>
                  <div class="col-md-12">
                    <textarea class="form-control" v-model="tasksForm.comment" rows="5"></textarea>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-12">
                    <button class="btn btn-success fRigh" @click.prevent="addTask()">Добавить задачу</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <div
        class="modal fade bs-closeTask-modal-md"
        tabindex="-1"
        role="dialog"
        aria-labelledby="myLargeModalLabel"
        aria-hidden="true"
        style="display: none;"
      >
        <div class="modal-dialog modal-md">
          <div class="modal-content">
            <div class="modal-header">
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                ref="tasksClose"
                aria-hidden="true"
              >×</button>
              <h4 class="modal-title" id="myLargeModalLabel">Закрыть задачу</h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal form-material">
                <div class="alert alert-danger" v-if="error.message">{{error.message}}</div>
                <div class="form-group">
                  <label class="col-sm-12">Результат по задаче</label>
                  <div class="col-md-12">
                    <textarea class="form-control" v-model="currentTask.result" rows="5"></textarea>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-6">
                    <button class="btn btn-danger" @click.prevent="closeTask()">Закрыть задачу</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <div
        class="modal fade bs-removeTask-modal-md"
        tabindex="-1"
        role="dialog"
        aria-labelledby="mySmallModalLabel"
        aria-hidden="true"
        style="display: none;"
      >
        <div class="modal-dialog modal-md">
          <div class="modal-content">
            <div class="modal-header">
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-hidden="true"
                ref="tasksCloseRemove"
              >×</button>
              <h4 class="modal-title" id="mySmallModalLabel">Удалить задачу?
                <b>N {{currentTask.id}}</b>
              </h4>
            </div>
            <div class="modal-body">
              <div class="alert alert-danger" v-if="error.message">{{error.message}}</div>
              <button class="btn btn-success button" data-dismiss="modal" aria-hidden="true">Нет</button>
              <button class="btn btn-danger" @click.prevent="removeTask()">Да</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Vue from "vue";
import { main } from "./mixins/main";
import { tasks } from "./mixins/tasks";
import { clients } from "./mixins/clients";
import datePicker from "vue-bootstrap-datetimepicker";
import authGuard from "./../components/guards/auth.guard";

export default {
  beforeRouteEnter: authGuard,
  data() {
    return {
      searching: false,
      data: {}
    };
  },
  watch: {
    $route(to, from) {
      this.getTasks();
    }
  },
  created() {
    (this.tasksFilter = { responsible: this.$route.params.id }), { id: "DESC" };
    this.managersFilter = { roleId: "!=|3" };
    this.getTasks();
    this.getManagers();
  },
  mounted() {},
  methods: {
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
  mixins: [main, tasks, clients],
  components: {
    datePicker: datePicker
  }
};
</script>
<style>
.elementSearch .elementSearch__item {
  cursor: pointer;
}
</style>
