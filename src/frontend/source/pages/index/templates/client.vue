<template>
  <div v-if="user && $route.params.id">
        <div id="page-wrapper">

            <div class="row" v-if="access"> 
              <div class="col-12">
                <div class="white-box">
                  <div class="alert alert-danger">{{access}}</div>
                </div>
              </div>
            </div>
            <div class="row">
                    <div class="col-md-6 col-xs-12">
                        <div class="white-box m-0 white-box-customtab">
                          <ul class="nav nav-tabs tabs customtab">
                                <li class="active tab">
                                    <a href="#client" data-toggle="tab">  <span>Основное</span> </a>
                                </li>
                                 <li class="tab">
                                    <a href="#client_leads" data-toggle="tab"><span>Лиды</span> </a>
                                </li>
                                 <li class="tab pull-right setting__tab">
                                    <span>
                                    <span data-toggle="dropdown" class="mdi mdi-settings"></span>
                                    <ul role="menu" class="dropdown-menu animated flipInY">
                                        <li><a href="javascript:void(0)"><i class="mdi mdi-printer"></i> Печать</a></li>
                                        <li><a href="javascript:void(0)"><i class="mdi mdi-file-excel"></i> Экспорт CSV</a></li>
                                        <li><a href="#" @click.prevent="removeModal()"><i class="fa fa-trash-o"></i> Удалить</a></li>
                                    </ul>
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <div class="white-box p-t-0 m-t-0">
                            
                            <div class="tab-content m-t-0">
                                <div class="tab-pane active" id="client">
                                    <form class="form-horizontal form-material">
                                        <div class="alert alert-success" v-if="response.active">Данные обновлены</div>
                                        <div class="alert alert-danger" v-if="error.message">{{error.message}}</div>
                                         <div class="form-group">
                                            <label class="col-sm-4 form-label-costume">Ответственный</label>
                                            <div class="col-sm-8">
                                                <select class="form-control form-control-line" v-model="clientform.responsible">
                                                  <optgroup v-for="group in managerGroups" :key="group.id" :label="group.name">
                                                    <option v-for="manager in group.users" :key="manager.id" :value="manager.id">{{manager.name}}</option>                                                 
                                                  </optgroup>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 form-label-costume">Имя</label>
                                            <div class="col-sm-8">
                                                <input type="text" v-model="clientform.name" class="form-control form-control-line"> </div>
                                        </div>
                                       
                                        
                                        <div class="form-group">
                                            <label class="col-sm-4 form-label-costume">Фамилия</label>
                                            <div class="col-sm-8">
                                                <input type="text" v-model="clientform.surname" class="form-control form-control-line"> </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 form-label-costume">Телефон</label>
                                            <div class="col-sm-8">
                                                <input type="text" v-model="clientform.phone" class="form-control form-control-line"> </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="col-sm-4 form-label-costume">Телефон подтвержден?</label>
                                            <div class="col-sm-8">
                                              <switches v-model="clientform.phoneVerified" theme="bulma" color="default"></switches>
                                            </div>
                                        </div>
                                         <div class="form-group">
                                            <label class="col-sm-4 form-label-costume">Дополнительный телефон</label>
                                            <div class="col-sm-8">
                                                <input type="text" v-model="clientform.otherPhone" class="form-control form-control-line"> </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="example-email" class="col-sm-4 form-label-costume">Email</label>
                                            <div class="col-sm-8">
                                                <input type="email" v-model="clientform.email" class="form-control form-control-line"  name="example-email" id="example-email"> </div>
                                        </div>
                                         <div class="form-group">
                                            <label class="col-sm-4 form-label-costume">Email подтвержден?</label>
                                            <div class="col-sm-8">
                                              <switches v-model="clientform.emailVerified" theme="bulma" color="default"></switches>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 form-label-costume">Страна</label>
                                            <div class="col-sm-8">
                                                <select class="form-control form-control-line" v-model="clientform.countryId">
                                                    <option v-for="country in countries" :key="country.id" :value="country.id">{{country.name}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 form-label-costume">Штат</label>
                                            <div class="col-sm-8">
                                                <input type="text" v-model="clientform.state" class="form-control form-control-line"> </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 form-label-costume">Город</label>
                                            <div class="col-sm-8">
                                                <input type="text" v-model="clientform.city" class="form-control form-control-line"> </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 form-label-costume">Улица</label>
                                            <div class="col-sm-8">
                                                <input type="text" v-model="clientform.street" class="form-control form-control-line"> </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 form-label-costume">Дом</label>
                                            <div class="col-sm-8">
                                                <input type="text" v-model="clientform.building" class="form-control form-control-line"> </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 form-label-costume">Квартира/офис</label>
                                            <div class="col-sm-8">
                                                <input type="text" v-model="clientform.flat" class="form-control form-control-line"> </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 form-label-costume">ZIP code</label>
                                            <div class="col-sm-8">
                                                <input type="text" v-model="clientform.zip" class="form-control form-control-line"> </div>
                                        </div>
                                       
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <button class="btn btn-success fRigh" @click.prevent="updateClient()">Обновить данные</button>
                                                 
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane" id="client_leads">
                                  <table class="table table-striped dataTable no-footer" role="grid">
                                      <tbody >
                                          <tr role="row" class="odd" v-for="lead in leads" :key="lead.id">
                                              <td><router-link :to="{ path: '/lead/' + lead.id}">{{lead.name}}</router-link></td>
                                              <td><span :class="'lead__status lead__status__'+lead.status">{{lead.statusName}}</span></td>
                                              <td>{{lead.budget}} €</td>
                                          </tr>
                                      </tbody>
                                  </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xs-12">
                      <div class="white-box m-0 white-box-customtab">
                          <ul class="nav nav-tabs tabs customtab">
                                <li class="active tab">
                                    <a href="#tab_note" data-toggle="tab">  <span>Примичания</span> </a>
                                </li>
                                 <li class="tab">
                                    <a href="#tab_update" data-toggle="tab"><span>Обновление</span> </a>
                                </li>
                                <li class="tab">
                                    <a href="#tab_tasks" data-toggle="tab"><span>Задачи</span> </a>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content white-box p-t-0 m-t-0">
                          
                          <div class="tab-pane active" id="tab_note">
                            <div class="alert alert-success" v-if="noteResponse.active">Примечание добавлено!</div>
                            <div class="alert alert-danger" v-if="noteError.message">{{noteError.message}}</div>
                            <ul class="notes">
                              <li class="note" v-for="note in notes" :key="note._id.$oid" v-if="note.noteType==$store.state.noteTypes.NOTE_TYPE_COMMON.id || note.noteType==$store.state.noteTypes.NOTE_TYPE_TASK_CREATED.id || note.noteType==$store.state.noteTypes.NOTE_TYPE_TASK_RESULT.id">
                                  <b v-if="note.dataValue.text">{{note.dataValue.text}}</b>
                                  <div class="taskBlock" v-if="note.noteType==$store.state.noteTypes.NOTE_TYPE_TASK_RESULT.id">
                                      <div class="taskBlock__item">
                                        Результат по задаче <b>N {{note.dataValue.data.task.id}}</b>: {{note.dataValue.data.result}}
                                      </div>
                                  </div>
                              </li>
                            </ul>
                            <form class="form-horizontal form-material">
                              <div class="form-group">
                                  <label class="col-sm-12">Примечание</label>
                                  <div class="col-md-12">
                                      <textarea class="form-control" v-model="noteComment" rows="5"></textarea>
                                  </div>
                              </div>
                              <div class="form-group">
                                  <div class="col-sm-12">
                                      <button class="btn btn-success fRigh" @click.prevent="saveNoteComment($store.state.elementTypes.ELEMENT_TYPE_CLIENT.id, clientInfo)">Сохранить</button>
                                  </div>
                              </div>
                            </form>
                          </div>
                          <div class="tab-pane " id="tab_update">
                            <ul class="notes">
                              <li class="note" v-for="note in notes" :key="note._id.$oid" v-if="note.noteType!=$store.state.noteTypes.NOTE_TYPE_COMMON.id">
                              
                                  <b>{{new Date(note.createdAt*1000) | dateTime}} {{noteTypes[note.noteType]}}</b>,
                                  создал: <router-link :to="'/user/' + note.createdBy">{{managersById[note.createdBy]?managersById[note.createdBy].name:note.dataValue.by}}</router-link>
                                  <b v-if="note.dataValue.text">{{note.dataValue.text}}</b>
                                  <span v-if="note.noteType == $store.state.noteTypes.NOTE_TYPE_CLIENT_FIELD_UPDATE.id && note.dataValue.data.field != 'countryId' && note.dataValue.data.field != 'responsible'"><b>{{note.dataValue.data.field}}: </b>{{note.dataValue.data.values.from?note.dataValue.data.values.from:'null'}} -> {{note.dataValue.data.values.to?note.dataValue.data.values.to:'null'}}</span>
                                  <span v-if="note.noteType == $store.state.noteTypes.NOTE_TYPE_CLIENT_FIELD_UPDATE.id && note.dataValue.data.field == 'countryId'"><b>country: </b>{{countriesById[note.dataValue.data.values.from].name}} -> {{countriesById[note.dataValue.data.values.to].name}}</span>
                                  <span v-if="note.noteType == $store.state.noteTypes.NOTE_TYPE_CLIENT_FIELD_UPDATE.id && note.dataValue.data.field == 'responsible'">
                                    <b>{{note.dataValue.data.field}}: </b>
                                    <router-link :to="'/user/' + note.dataValue.data.values.from">{{managersById[note.dataValue.data.values.from]?managersById[note.dataValue.data.values.from].name:'from'}}</router-link> -> 
                                    <router-link :to="'/user/' + note.dataValue.data.values.to">{{managersById[note.dataValue.data.values.to]?managersById[note.dataValue.data.values.to].name:'to'}}</router-link>
                                  </span>
               
                              </li>
                            </ul>
                          </div>
                          <div class="tab-pane" id="tab_tasks">
                            <div class="taskBlock">
                                <div v-if="tasks.length === 0" alt="default" data-toggle="modal" data-target=".bs-taskAdd-modal-sm" class="model_img img-responsive">
                                    <span class="circle circle-sm bg-success di"><i class="ti-plus"></i></span> 
                                </div>
                                <div v-for="task in tasks" :key="task.id" :class="Date.now() < new Date(task.eventDate.date)?'taskBlock__item active':'taskBlock__item'">
                                  Задачa <b>N {{task.id}}</b> создал: <router-link :to="'/user/'  + task.createdBy">{{task.createdByName}}</router-link> <b>{{task.eventDate.date | dateTime}}</b> в <router-link :to="'/client/'  + task.elementId">Клиент</router-link><br/>
                                  Тип: {{task.type=='connect'?'Связаться с клиентом':task.type=='meeting'?'Встреча':'Другое'}}<br/>
                                  {{task.comment}} <br/>
                                  <span v-if="user.id == task.responsible + '-' + task.responsibleSecret || user.id == task.createdBy + '-' + task.createdBySecret">
                                    <a href="#" @click.prevent="getTaskData(task.id)" alt="default" data-toggle="modal" data-target=".bs-closeTask-modal-md">
                                      Закрыть
                                    </a>
                                    <a href="#" @click.prevent="getTaskData(task.id)" alt="default" data-toggle="modal" data-target=".bs-removeTask-modal-md">
                                      Удалить
                                    </a>
                                  </span>
                                </div>
                            </div>
                          </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade bs-taskAdd-modal-sm" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title" id="myLargeModalLabel">Добавить задачу</h4> </div>
                            <div class="modal-body">
                                <form class="form-horizontal form-material">
                                    <div class="alert alert-success" v-if="response.active">задача добавлена</div>
                                    <div class="alert alert-danger" v-if="error.message">{{error.message}}</div>
                                    <div class="form-group">
                                        <label class="col-sm-12">Ответственный</label>
                                        <div class="col-sm-12">
                                            <select class="form-control form-control-line" v-model="tasksForm.responsible">
                                            <optgroup v-for="group in managerGroups" :key="group.id" :label="group.name">
                                                <option v-for="manager in group.users" :key="manager.id" :value="manager.id">{{manager.name}}</option>                                                 
                                            </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-12">Тип</label>
                                        <div class="col-sm-12">
                                            <select class="form-control form-control-line" v-model="tasksForm.type">
                                                <option v-for="taskType in $store.state.taskTypes" :key="taskType.value" :value="taskType.value">{{taskType.title}}</option>
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
                                            <button class="btn btn-success fRigh" @click.prevent="addTask(true)">Добавить задачу</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>
                <div class="modal fade bs-closeTask-modal-md" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
                  <div class="modal-dialog modal-md">
                      <div class="modal-content">
                          <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal" ref="tasksClose" aria-hidden="true">×</button>
                              <h4 class="modal-title" id="myLargeModalLabel">Закрыть задачу</h4> </div>
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
                                          <button class="btn btn-danger " @click.prevent="closeTask()">Закрыть задачу</button>
                                      </div>
                                  </div>
                              </form>
                          </div>
                      </div>
                      <!-- /.modal-content -->
                  </div>
                  <!-- /.modal-dialog -->
              </div>
              <div class="modal fade bs-removeTask-modal-md" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
                  <div class="modal-dialog modal-md">
                      <div class="modal-content">
                          <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ref="tasksCloseRemove">×</button>
                              <h4 class="modal-title" id="mySmallModalLabel">Удалить задачу? <b>N {{currentTask.id}}</b></h4> </div>
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
import Switches from 'vue-switches';
import { main } from "./mixins/main";
import { notes } from "./mixins/notes";
import { tasks } from "./mixins/tasks";
import { clients } from "./mixins/clients";
import { leads } from "./mixins/leads";
import datePicker from "vue-bootstrap-datetimepicker";
import authGuard from "./../components/guards/auth.guard";

export default {
  beforeRouteEnter: authGuard,
  data() {
    return {
      data: {}
    };
  },
  created() {
    const { state } = this.$store;
    this.tasksForm.elementId = this.$route.params.id;
    this.tasksForm.elementType = state.elementTypes.ELEMENT_TYPE_CLIENT.id;
    this.tasksFilter = { elementId: this.$route.params.id, elementType: state.elementTypes.ELEMENT_TYPE_CLIENT.id};
    this.managersFilter = { roleId: 2 };
    this.clientsFilter = { client: this.$route.params.id };

    this.getParams(["countries"]);
    this.getManagers();
    this.getTasks();
    this.getClient();
    this.getLeads();
  },
  mounted() {    
  },
  computed: {
  },
   methods: {
    removeModal() {
      const vm = this;
      swal(
        {
          title: "Вы уверены?",
          text: "Все данные о клиенте буду удаленны!",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "Да, удалить!",
          cancelButtonText: "Отмена",
          closeOnConfirm: false
          //closeOnCancel: false
        },
        function() {
          vm.removeClients(true);
        }
      );
    }
  },
  mixins: [main, notes, tasks, clients, leads],    
  components: {
    Switches: Switches,
    datePicker: datePicker
  }
};
</script>

<style>
.form-label-costume {
  min-height: 38px;
  font-size: 14px;
  padding-top: 12px;
  font-weight: 400;
}
.form-control {
  font-size: 14px;
}
.nav > li.setting__tab .mdi-settings {
  padding-right: 0;
  color: #999;
  cursor: pointer;
}
.nav > li.setting__tab .dropdown-menu {
  left: auto;
  right: 0;
}
.nav > li.setting__tab .open .mdi-settings {
  color: #000;
}
.nav > li.setting__tab .dropdown-menu {
  margin-top: 2px;
  padding: 0;
}
.nav > li.setting__tab .dropdown-menu > li > a {
  font-weight: 300;
}
.nav > li.setting__tab .dropdown-menu > li > a .fa-trash-o {
  margin-right: 3px;
  color: #e38c89;
}
.nav > li.setting__tab .dropdown-menu > li > a .mdi-file-excel {
  margin-left: -2px;
  margin-top: 1px;
  color: #548654;
}
.nav > li.setting__tab .dropdown-menu > li > a .mdi-printer {
  margin-left: -2px;
  margin-top: 1px;
}
.vue-switcher{
  margin-top: 15px
}
.vue-switcher div{
  background-color: #90d0ec !important;
}
.vue-switcher div:after{
  background-color: #2cabe3 !important;
}
.vue-switcher--unchecked div{
  background-color: #bfbfbf !important;
}
.vue-switcher--unchecked div:after{
  background-color: gray !important;
}
</style>