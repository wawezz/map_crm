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
                                                            <div v-if="checkedLeads.length>0" class="btn btn-danger" data-toggle="modal" data-target=".bs-leadsRemove-modal-md">Удалить лиды?</div>
                                                        </li>
                                                        <li>
                                                            <div alt="default" data-toggle="modal" data-target=".bs-leadAdd-modal-lg" class="model_img img-responsive">
                                                                <span class="circle circle-sm bg-success di"><i class="ti-plus"></i></span><span>Добавить лид</span>
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
                                                            <th>ID</th>
                                                            <th>ДАТА СОЗДАНИЯ</th> 
                                                            <th>ДАТА ПОЛУЧЕНИЯ</th>
                                                            <th>НАЗВАНИЕ</th>
                                                            <th>КОНТАКТ</th>
                                                            <th>ОТВЕТСТВЕННЫЙ</th>
                                                            <th>СТАТУС</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody v-for="listLead in leads" :key="listLead.id">
                                                        <tr class="advance-table-row">
                                                            <td style="width: 10px;"></td>
                                                            <td style="width: 40px;">
                                                                <div class="checkbox checkbox-circle checkbox-info">
                                                                    <input :id="'checkbox23_' + listLead.id" type="checkbox" :value="listLead.id" v-model="checkedLeads">
                                                                    <label :for="'checkbox23_' + listLead.id"> </label>
                                                                </div>
                                                            </td>
                                                            <td>{{listLead.id}}</td>
                                                            <td>{{listLead.createdAt.date | date}}</td>
                                                            <td>{{listLead.completedAt.date | date}}</td>
                                                            <td><router-link :to="{ path: '/lead/' + listLead.id}">{{listLead.name}}</router-link></td>
                                                            <td><router-link :to="{ path: '/client/' + listLead.client}">{{listLead.clientName}}</router-link></td>
                                                            <td><router-link :to="{ path: '/user/' + listLead.responsible}">{{listLead.responsibleName}}</router-link></td>
                                                            <td>{{listLead.statusName}}</td>
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
            <div class="modal fade bs-leadAdd-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title" id="myLargeModalLabel">Добавить лид</h4> </div>
                        <div class="modal-body">
                            <form class="form-horizontal form-material">
                                <div class="alert alert-success" v-if="response.active">лид добавлен</div>
                                <div class="alert alert-danger" v-if="error.message">{{error.message}}</div>
                                <div class="form-group">
                                    <label class="col-md-12">Название</label>
                                    <div class="col-md-12">
                                        <input type="text" v-model="leadsform.name" placeholder='New lead' class="form-control form-control-line"> </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-12">Ответственный</label>
                                    <div class="col-sm-12">
                                        <select class="form-control form-control-line" v-model="leadsform.responsible">
                                          <optgroup v-for="group in managerGroups" :key="group.id" :label="group.name">
                                            <option v-for="manager in group.users" :key="manager.id" :value="manager.id">{{manager.name}}</option>                                                 
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
                                            <input id="checkboxPhone" v-model="leadsform.postOrder" :checked="leadsform.postOrder" type="checkbox">
                                            <label for="checkboxPhone">Разместить заказ?</label>
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
                                        <button class="btn btn-success fRigh" @click.prevent="addLead()">Добавить лид</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <div class="modal fade bs-leadsRemove-modal-md" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ref="leadsRemoveClose">×</button>
                                <h4 class="modal-title" id="mySmallModalLabel">Удалить лиды?</h4> </div>
                            <div class="modal-body">
                              <button class="btn btn-success button" data-dismiss="modal" aria-hidden="true">Нет</button>
                              <button class="btn btn-danger" @click.prevent="removeLeads()">Да</button>
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
import { leads } from "./mixins/leads";
import { clients } from "./mixins/clients";
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
    this.managersFilter = { roleId: 2 };
    this.getParams(["statuses", "countries", "currencies"]);
    this.getManagers();
    this.getClients();
    this.getProducts();
    this.getLeads();
  },
  mounted() {},
  methods: {    
  },
  mixins: [main, leads, clients],
  components: {
    "date-picker": datePicker
  }
};
</script>