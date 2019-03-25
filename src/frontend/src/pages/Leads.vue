<template>
  <div class="row" v-if="user">
    <div class="col-sm-12 mb-3">
      <base-button
        data-toggle="modal"
        data-target="leadAdd"
        type="button"
        class="btn btn-primary"
        @click="leadAddModalVisible = true"
      >
        <i class="tim-icons icon-simple-add"></i> Add lead
      </base-button>
      <base-button
        v-if="checkedLeads.length>0"
        data-toggle="modal"
        data-target="leadsRemove"
        type="button"
        class="btn btn-danger ml-3"
        @click="leadsRemoveModalVisible = true"
      >
        <i class="tim-icons icon-trash-simple"></i> Remove leads?
      </base-button>
    </div>
    <div class="col-sm-12">
      <base-alert v-if="listError" type="danger">{{listError}}</base-alert>
    </div>
    <div class="col-12">
      <card title="Leads">
        <div class="table">
          <table class="table tablesorter">
            <thead>
              <tr>
                <th></th>
                <th></th>
                <th>ID</th>
                <th>DATE OF CREATE</th>
                <th>DATE OF COMPLETED</th>
                <th>TITLE</th>
                <th>CONTACT</th>
                <th>RESPONSIBLE</th>
                <th>STATUS</th>
              </tr>
            </thead>
            <tbody v-for="listLead in leads" :key="listLead.id">
              <tr class="advance-table-row">
                <td style="width: 10px;"></td>
                <td style="width: 40px;">
                  <base-checkbox
                    :id="'checkbox_' + listLead.id"
                    :value="listLead.id"
                    v-model="checkedLeads"
                  ></base-checkbox>
                </td>
                <td>{{listLead.id}}</td>
                <td>{{listLead.createdAt.date | date}}</td>
                <td>{{listLead.completedAt.date | date}}</td>
                <td>
                  <router-link :to="{ path: '/lead/' + listLead.id}">{{listLead.name}}</router-link>
                </td>
                <td>
                  <router-link :to="{ path: '/client/' + listLead.client}">{{listLead.clientName}}</router-link>
                </td>
                <td>
                  <router-link
                    :to="{ path: '/profile/' + listLead.responsible}"
                  >{{listLead.responsibleName}}</router-link>
                </td>
                <td>{{listLead.statusName}}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="col-12">
          <pagination
            v-if="leadsTotalCount"
            :total="leadsTotalCount"
            :current="leadsPage"
            :size="leadsLimit"
            :prefix="'/leads/'"
          />
        </div>
        <modal
          modalClasses="modal-lg"
          :show.sync="leadAddModalVisible"
          id="leadAdd"
          :centered="false"
          :show-close="true"
        >
          <h4 class="black">Add lead</h4>
          <div class="row">
            <div class="col-12">
              <base-alert v-if="error.message" type="danger">{{error.message}}</base-alert>
              <base-alert v-if="response.active" type="success">lead added</base-alert>
            </div>
          </div>
          <card>
            <div class="row">
              <div class="col-4">
                <base-input label="Name" v-model="leadsform.name"></base-input>
              </div>
              <div class="col-4">
                <label>Responsible</label>
                <select class="form-control form-control-line" v-model="leadsform.responsible">
                  <optgroup v-for="group in managerGroups" :key="group.id" :label="group.name">
                    <option
                      v-for="manager in group.users"
                      :key="manager.id"
                      :value="manager.id"
                    >{{manager.name}}</option>
                  </optgroup>
                </select>
              </div>
              <div class="col-4">
                <label>Status</label>
                <select class="form-control form-control-line" v-model="leadsform.status">
                  <option
                    v-for="status in statuses"
                    :key="status.id"
                    :value="status.id"
                  >{{status.name}}</option>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col-4">
                <label>Client</label>
                <select class="form-control form-control-line" v-model="leadsform.client">
                  <option
                    v-for="client in clients"
                    :key="client.id"
                    :value="client.id"
                  >{{client.name}}</option>
                </select>
              </div>
              <div class="col-4">
                <label>Country</label>
                <select
                  class="form-control form-control-line"
                  @change="updateCurrency()"
                  v-model="leadsform.countryId"
                >
                  <option
                    v-for="(country, key) in countries"
                    :key="key"
                    :value="country.id"
                  >{{country.name}}</option>
                </select>
              </div>
              <div class="col-4">
                <label>Currency</label>
                <select class="form-control form-control-line" v-model="leadsform.currency">
                  <option
                    v-for="currency in currenciesFiltered"
                    :key="currency.id"
                    :value="currency.id"
                  >{{currency.name}}</option>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col-3">
                <base-input
                  label="Budget"
                  type="number"
                  min="0"
                  v-model="leadsform.budget"
                  placeholder="0"
                ></base-input>
              </div>
              <div class="col-3">
                <label>Product</label>
                <select class="form-control form-control-line" v-model="leadsform.product">
                  <option
                    v-for="product in products"
                    :key="product.id"
                    :value="product.id"
                  >{{product.name}}</option>
                </select>
              </div>
              <div class="col-3">
                <label>Count</label>
                <select class="form-control form-control-line" v-model="leadsform.productCount">
                  <option v-for="n in 10" :key="n" :value="n">{{n}}</option>
                </select>
              </div>
              <div class="col-3">
                <base-input
                  label="Product price"
                  type="number"
                  min="0"
                  v-model="leadsform.productPrice"
                  placeholder="0"
                ></base-input>
              </div>
            </div>
            <div class="row">
              <div class="col-3">
                <base-input
                  label="Delivery price"
                  type="number"
                  min="0"
                  v-model="leadsform.shippingPrice"
                  placeholder="0"
                ></base-input>
              </div>
              <div class="col-3">
                <label>Date of order</label>
                <datepicker v-model="leadsform.completedAt" :format="config.date.format"></datepicker>
              </div>
              <div class="col-3">
                <label>The first call to the client</label>
                <datepicker v-model="leadsform.firstCallAt" :format="config.date.format"></datepicker>
              </div>
              <div class="col-3 centeredFlex">
                <base-checkbox
                  label="Place an order?"
                  :value="leadsform.postOrder"
                  v-model="leadsform.postOrder"
                ></base-checkbox>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <base-input>
                  <label>Rejection reason</label>
                  <textarea rows="4" class="form-control" v-model="leadsform.rejectionReason"></textarea>
                </base-input>
              </div>
            </div>
            <base-button
              slot="footer"
              type="primary"
              class="text-center"
              fill
              @click.prevent="addLead"
            >Add lead</base-button>
          </card>
        </modal>
        <modal
          :show.sync="leadsRemoveModalVisible"
          id="placeLeadsRemove"
          :centered="false"
          :show-close="true"
          class="text-center"
        >
          <h4 class="black">Remove leads?</h4>
          <button class="btn btn-default" @click="leadsRemoveModalVisible = false">No</button>
          <button class="btn btn-danger" @click.prevent="removeLeads(false)">Yes</button>
        </modal>
      </card>
    </div>
  </div>
</template>
<script>
import { BaseAlert } from "@/components";
import { Pagination } from "@/components";
import { Modal } from "@/components";
import { main } from "./../mixins/main";
import { leads } from "./../mixins/leads";
import { clients } from "./../mixins/clients";
import Datepicker from "vuejs-datepicker";
import authGuard from "./../guards/auth.guard";

export default {
  beforeRouteEnter: authGuard,
  components: {
    BaseAlert,
    Modal,
    Datepicker,
    Pagination
  },
  watch: {
    $route(to, from) {
      this.getLeads();
    }
  },
  data() {
    return {
      leadAddModalVisible: false,
      leadsRemoveModalVisible: false
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
  mixins: [main, leads, clients]
};
</script>
<style>
</style>
