<template>
  <div v-if="user && $route.params.id">
        <div id="page-wrapper">
          <div class="container-fluid p-t-20">
              <div class="row" v-if="access"> 
                <div class="col-12">
                  <div class="white-box">
                    <div class="alert alert-danger">{{access}}</div>
                  </div>
                </div>
              </div>

              <div class="row">
                      <div class="col-md-6 col-xs-12">
                          <div class="white-box">
                              <ul class="nav nav-tabs tabs customtab">
                                  <li class="active tab">
                                      <a href="#profile" data-toggle="tab"> <span class="visible-xs"><i class="fa fa-user"></i></span> <span class="hidden-xs">Лид</span> </a>
                                  </li>
                              </ul>
                              <div class="tab-content">
                                  <div class="tab-pane active" id="profile">
                                      <form class="form-horizontal form-material">
                                          <div class="alert alert-success" v-if="response.active">Данные обновлены</div>
                                          <div class="alert alert-danger" v-if="error.message">{{error.message}}</div>
                                          <div class="form-group">
                                              <label class="col-md-12">Название</label>
                                              <div class="col-md-12">
                                                  <input type="text" v-model="leadsform.name" class="form-control form-control-line"> </div>
                                          </div>
                                          <div class="form-group">
                                              <label class="col-sm-12">Ответственный</label>
                                              <div class="col-sm-12">
                                                  <select class="form-control form-control-line" v-model="leadsform.responsible">
                                                    <optgroup v-for="(data, group) in managerGroups" v-if="group != 'group_0'" :key="group" :label="data.name">
                                                      <option v-for="manager in data.users" :key="manager.id" :value="manager.id">{{manager.name}}</option>                                                 
                                                    </optgroup>
                                                  </select>
                                              </div>
                                          </div>
                                          <div class="form-group">
                                              <label class="col-sm-12">Клиент</label>
                                              <div class="col-sm-12">
                                                  <select class="form-control form-control-line" v-model="leadsform.client">
                                                      <option v-for="client in clients" :key="client.id" :value="client.id">{{client.name}}</option>                                                 
                                                  </select>
                                              </div>
                                          </div>
                                          <div class="form-group">
                                              <label class="col-sm-12">Статус</label>
                                              <div class="col-sm-12">
                                                  <select class="form-control form-control-line" v-model="leadsform.status">
                                                      <option v-for="status in statuses" :key="status.id" :value="status.id">{{status.name}}</option>
                                                  </select>
                                              </div>
                                          </div>
                                          <div class="form-group">
                                              <label class="col-md-12">Бюджет</label>
                                              <div class="col-md-12">
                                                  <input type="number" min="0" v-model="leadsform.budget" placeholder='0' class="form-control form-control-line"> </div>
                                          </div>
                                          <div class="form-group">
                                              <label class="col-md-12">Первый звонок клиенту</label>
                                              <div class="col-md-12">
                                                  <date-picker v-model="leadsform.firstCallAt" :config="config.date"></date-picker>
                                              </div>
                                          </div>
                                          <div class="form-group">
                                              <label class="col-sm-12">Страна</label>
                                              <div class="col-sm-12">
                                                  <select class="form-control form-control-line" @change="updateCurrency()" v-model="leadsform.countryId">
                                                      <option v-for="(country, key) in countries" :key="key" :value="country.id">{{country.name}}</option>
                                                  </select>
                                              </div>
                                          </div>
                                          <div class="form-group">
                                              <label class="col-sm-12">Валюта</label>
                                              <div class="col-sm-12">
                                                  <select class="form-control form-control-line" v-model="leadsform.currency">
                                                      <option v-for="currency in currenciesFiltered" :key="currency.id" :value="currency.id">{{currency.name}}</option>
                                                  </select>
                                              </div>
                                          </div>
                                          <div class="form-group">
                                              <label class="col-sm-12">Дата оформления заказа</label>
                                              <div class="col-md-12">
                                                  <date-picker v-model="leadsform.completedAt" :config="config.date"></date-picker>
                                              </div>
                                          </div>
                                          <div class="form-group">
                                              <label class="col-sm-12">Продукт</label>
                                              <div class="col-sm-12">
                                                  <select class="form-control form-control-line" v-model="leadsform.product">
                                                      <option v-for="product in products" :key="product.id" :value="product.id">{{product.name}}</option>
                                                  </select>
                                              </div>
                                          </div>
                                          <div class="form-group">
                                              <label class="col-sm-12">Кол-во</label>
                                              <div class="col-sm-12">
                                                  <select class="form-control form:-control-line" v-model="leadsform.productCount">
                                                      <option v-for="n in 10" :key="n" :value="n">{{n}}</option>
                                                  </select>
                                              </div>
                                          </div>
                                          <div class="form-group">
                                              <label class="col-sm-12">Цена продукта</label>
                                              <div class="col-md-12">
                                                  <input type="number" min="0" v-model="leadsform.productPrice" placeholder='0' class="form-control form-control-line"> </div>
                                          </div>
                                          <div class="form-group">
                                              <label class="col-sm-12">Кросс-продукт</label>
                                              <div class="col-sm-12">
                                                  <select class="form-control form-control-line" v-model="leadsform.crossProduct">
                                                      <option v-for="product in products" :key="product.id" :value="product.id">{{product.name}}</option>
                                                  </select>
                                              </div>
                                          </div>
                                          <div class="form-group">
                                              <label class="col-sm-12">Кросс-продукт кол-во</label>
                                              <div class="col-sm-12">
                                                  <select class="form-control form:-control-line" v-model="leadsform.crossProductCount">
                                                      <option v-for="n in 10" :key="n" :value="n">{{n}}</option>
                                                  </select>
                                              </div>
                                          </div>
                                          <div class="form-group">
                                              <label class="col-sm-12">Цена кросс-продукта</label>
                                              <div class="col-md-12">
                                                  <input type="number" min="0" v-model="leadsform.crossProductPrice" placeholder='0' class="form-control form-control-line"> </div>
                                          </div>
                                          <div class="form-group">
                                              <label class="col-sm-12">Апселл-продукт</label>
                                              <div class="col-sm-12">
                                                  <select class="form-control form-control-line" v-model="leadsform.upsellProduct">
                                                      <option v-for="product in products" :key="product.id" :value="product.id">{{product.name}}</option>
                                                  </select>
                                              </div>
                                          </div>
                                          <div class="form-group">
                                              <label class="col-sm-12">Апселл-продукт кол-во</label>
                                              <div class="col-sm-12">
                                                  <select class="form-control form:-control-line" v-model="leadsform.upsellProductCount">
                                                      <option v-for="n in 10" :key="n" :value="n">{{n}}</option>
                                                  </select>
                                              </div>
                                          </div>
                                          <div class="form-group">
                                              <label class="col-sm-12">Цена апселл-продукта</label>
                                              <div class="col-md-12">
                                                  <input type="number" min="0" v-model="leadsform.upsellProductPrice" placeholder='0' class="form-control form-control-line"> </div>
                                          </div>
                                          <div class="form-group">
                                              <label class="col-sm-12">Цена доставки</label>
                                              <div class="col-md-12">
                                                  <input type="number" min="0" v-model="leadsform.shippingPrice" placeholder='0' class="form-control form-control-line"> </div>
                                          </div>
                                          <div class="form-group">
                                              <div class="col-md-12">
                                                  <div class="checkbox checkbox-success">
                                                      <input id="checkboxPost" v-model="leadsform.postOrder" :checked="leadsform.postOrder" type="checkbox">
                                                      <label for="checkboxPost">Разместить заказ?</label>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="form-group">
                                              <label class="col-sm-12">Причина отказа</label>
                                              <div class="col-md-12">
                                                  <textarea class="form-control" v-model="leadsform.rejectionReason" rows="5"></textarea>
                                              </div>
                                          </div>
                                          <div class="form-group">
                                              <div class="col-sm-12">
                                                  <button class="btn btn-success fRigh" @click.prevent="updateLead()">Обновить лид</button>
                                                  <div class="btn btn-danger" data-toggle="modal" data-target=".bs-leadRemove-modal-md">Удалить лид</div>
                                              </div>
                                          </div>
                                      </form>
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
                                      <button class="btn btn-success fRigh" @click.prevent="saveNoteComment($store.state.elementTypes.ELEMENT_TYPE_LEAD.id, leadInfo)">Сохранить</button>
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
                                  Задачa <b>N {{task.id}}</b> создал: <router-link :to="'/user/'  + task.createdBy">{{task.createdByName}}</router-link> <b>{{task.eventDate.date | dateTime}}</b> в <router-link :to="'/lead/'  + task.elementId">Лид</router-link><br/>
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
                  <div class="modal fade bs-leadRemove-modal-md" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
                      <div class="modal-dialog modal-md">
                          <div class="modal-content">
                              <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ref="leadsRemoveClose">×</button>
                                  <h4 class="modal-title" id="mySmallModalLabel">Удалить лид <b>{{leadInfo.name}}</b>?</h4> </div>
                              <div class="modal-body">
                                <button class="btn btn-success button" data-dismiss="modal" aria-hidden="true">Нет</button>
                                <button class="btn btn-danger" @click.prevent="removeLeads(true)">Да</button>
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
  </div>
</template>

<script>
import Vue from "vue";
import { main } from "./mixins/main";
import { notes } from "./mixins/notes";
import { leads } from "./mixins/leads";
import { clients } from "./mixins/clients";
import { tasks } from "./mixins/tasks";
import datePicker from "vue-bootstrap-datetimepicker";
import "eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css";
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
    this.tasksForm.elementType = state.elementTypes.ELEMENT_TYPE_LEAD.id;
    this.tasksFilter = { elementId: this.$route.params.id, elementType: state.elementTypes.ELEMENT_TYPE_LEAD.id};
    this.managersFilter = { roleId: 2 };
    
    this.getParams(["statuses", "countries", "currencies"]);
    this.getManagers();
    this.getClients();
    this.getProducts();
    this.getLead();
    this.getTasks();
  },
  mounted() {
  },
  computed: {
  },
  methods: {
  
  },
  mixins: [main, notes, leads, clients, tasks],
  components: {
    "date-picker": datePicker
  }
};
</script>

<style>
</style>