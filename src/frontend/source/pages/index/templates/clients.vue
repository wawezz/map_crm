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
                                                            <div v-if="checkedClients.length>0" class="btn btn-danger" data-toggle="modal" data-target=".bs-clientsRemove-modal-md">Удалить контакты?</div>
                                                        </li>
                                                        <li>
                                                            <div alt="default" data-toggle="modal" data-target=".bs-clientAdd-modal-lg" class="model_img img-responsive">
                                                                <span class="circle circle-sm bg-success di"><i class="ti-plus"></i></span><span>Добавить контакт</span>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="table-responsive manage-table">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th></th>
                                                            <th></th>
                                                            <th>ИМЯ</th>
                                                            <th>ТЕЛЕФОН</th> 
                                                            <th>EMAIL</th>
                                                            <th>ДОБАВИЛ</th>
                                                            <th>ОТВЕТСТВЕННЫЙ</th>
                                                            <th>СТРАНА</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody v-for="listClient in clients" :key="listClient.id">
                                                        <tr class="advance-table-row">
                                                            <td style="width: 10px;"></td>
                                                            <td style="width: 40px;">
                                                                <div class="checkbox checkbox-circle checkbox-info">
                                                                    <input :id="'checkbox23_' + listClient.id" type="checkbox" :value="listClient.id" v-model="checkedClients">
                                                                    <label :for="'checkbox23_' + listClient.id"> </label>
                                                                </div>
                                                            </td>
                                                            <td><router-link :to="{ path: '/client/' + listClient.id}">{{listClient.name}}</router-link></td>
                                                            <td><a href="#">{{listClient.workPhone}}</a></td>
                                                            <td><a :href="'mail-to:' + listClient.email">{{listClient.email}}</a></td>
                                                            <td>{{listClient.creatorName}}</td>
                                                            <td>{{listClient.responsibleName}}</td>
                                                            <td>{{listClient.countryName}}</td>
                                                        </tr>    
                                                    </tbody>
                                                </table>
                                            </div>
                                        </section>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div> 
            <div class="modal fade bs-clientAdd-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title" id="myLargeModalLabel">Добавить клиента</h4> </div>
                        <div class="modal-body">
                            <form class="form-horizontal form-material">
                                <div class="alert alert-success" v-if="response.active">клиент добавлен</div>
                                <div class="alert alert-danger" v-if="error.message">{{error.message}}</div>
                                <div class="form-group">
                                    <label class="col-md-12">Имя</label>
                                    <div class="col-md-12">
                                        <input type="text" v-model="clientform.name" placeholder='Joe Division' class="form-control form-control-line"> </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-12">Фамилия</label>
                                    <div class="col-md-12">
                                        <input type="text" v-model="clientform.surname" class="form-control form-control-line"> </div>
                                </div>
                                <div class="form-group">
                                    <label for="example-email" class="col-md-12">Email</label>
                                    <div class="col-md-12">
                                        <input type="email" v-model="clientform.email" placeholder='Aphex_Twin@gmail.com' class="form-control form-control-line" name="example-email" id="example-email"> </div>
                                </div>
                                <div class="form-group">
                                    <label for="example-email" class="col-md-12">Телефон</label>
                                    <div class="col-md-12">
                                        <input type="text" v-model="clientform.phone" placeholder='+799911122233' class="form-control form-control-line" name="example-email" id="example-email"> </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-12">Дополнительный телефон</label>
                                    <div class="col-md-12">
                                        <input type="text" v-model="clientform.otherPhone" class="form-control form-control-line"> </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-12">Ответственный</label>
                                    <div class="col-sm-12">
                                        <select class="form-control form-control-line" v-model="clientform.responsible">
                                          <optgroup v-for="group in managerGroups" :key="group.id" :label="group.name">
                                            <option v-for="manager in group.users" :key="manager.id" :value="manager.id">{{manager.name}}</option>                                                 
                                          </optgroup>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-12">Выберете страну</label>
                                    <div class="col-sm-12">
                                        <select class="form-control form-control-line" v-model="clientform.countryId">
                                            <option v-for="country in countries" :key="country.id" :value="country.id">{{country.name}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-12">Штат</label>
                                    <div class="col-md-12">
                                        <input type="text" v-model="clientform.state" class="form-control form-control-line"> </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-12">Город</label>
                                    <div class="col-md-12">
                                        <input type="text" v-model="clientform.city" class="form-control form-control-line"> </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-12">Улица</label>
                                    <div class="col-md-12">
                                        <input type="text" v-model="clientform.street" class="form-control form-control-line"> </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-12">Дом</label>
                                    <div class="col-md-12">
                                        <input type="text" v-model="clientform.building" class="form-control form-control-line"> </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-12">Квартира/офис</label>
                                    <div class="col-md-12">
                                        <input type="text" v-model="clientform.flat" class="form-control form-control-line"> </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-12">ZIP code</label>
                                    <div class="col-md-12">
                                        <input type="text" v-model="clientform.zip" class="form-control form-control-line"> </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="checkbox checkbox-success">
                                            <input id="checkboxEmail" v-model="clientform.emailVerified" :checked="clientform.emailVerified" type="checkbox">
                                            <label for="checkboxEmail">Email подтвержден?</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="checkbox checkbox-success">
                                            <input id="checkboxPhone" v-model="clientform.phoneVerified" :checked="clientform.phoneVerified" type="checkbox">
                                            <label for="checkboxPhone">Телефон подтвержден?</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <button class="btn btn-success fRigh" @click.prevent="addClient()">Добавить клиента</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <div class="modal fade bs-clientsRemove-modal-md" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ref="close">×</button>
                                <h4 class="modal-title" id="mySmallModalLabel">Удалить клиентов?</h4> </div>
                            <div class="modal-body">
                              <button class="btn btn-success button" data-dismiss="modal" aria-hidden="true">Нет</button>
                              <button class="btn btn-danger" @click.prevent="removeClients()">Да</button>
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
import { clients } from "./mixins/clients";
import authGuard from "./../components/guards/auth.guard";

export default {
  beforeRouteEnter: authGuard,
  data() {
    return {
      data: {}
    };
  },
  created() {
    this.managersFilter = { roleId: 2 };
    this.getParams(["countries"]);
    this.getManagers();
    this.getClients();
  },
  mounted() {},
  computed: {

  },
  methods: {
  },
  mixins: [main, clients],  
  components: {
  }
};
</script>