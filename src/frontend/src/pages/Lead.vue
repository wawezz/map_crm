<template>
  <div class="row" v-if="user && $route.params.id">
    <div class="col-md-7">
      <div>
        <base-alert v-if="access" type="danger">{{access}}</base-alert>
        <base-alert v-if="response.active" type="success">Success</base-alert>
        <base-alert v-if="error.message" type="warning">{{error.message}}</base-alert>
      </div>
      <card>
        <h5 slot="header" class="title">Lead</h5>
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
              <option v-for="status in statuses" :key="status.id" :value="status.id">{{status.name}}</option>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="col-3">
            <label>Client</label>
            <select class="form-control form-control-line" v-model="leadsform.client">
              <option v-for="client in clients" :key="client.id" :value="client.id">{{client.name}}</option>
            </select>
          </div>
          <div class="col-3">
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
          <div class="col-3">
            <label>Currency</label>
            <select class="form-control form-control-line" v-model="leadsform.currency">
              <option
                v-for="currency in currenciesFiltered"
                :key="currency.id"
                :value="currency.id"
              >{{currency.name}}</option>
            </select>
          </div>
          <div class="col-3">
            <base-input
              label="Budget"
              type="number"
              min="0"
              v-model="leadsform.budget"
              placeholder="0"
            ></base-input>
          </div>
        </div>
        <div class="row">
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
          <div class="col-3">
            <base-input
              label="Delivery price"
              type="number"
              min="0"
              v-model="leadsform.shippingPrice"
              placeholder="0"
            ></base-input>
          </div>
        </div>
        <div class="row">
          <div class="col-4">
            <label>Date of order</label>
            <datepicker v-model="leadsform.completedAt" :format="config.date.format"></datepicker>
          </div>
          <div class="col-4">
            <label>First call date</label>
            <datepicker v-model="leadsform.firstCallAt" :format="config.date.format"></datepicker>
          </div>
          <div class="col-4 centeredFlex">
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
          class="ml-md-2 mr-md-2"
          fill
          @click.prevent="updateLead"
        >Update lead</base-button>
        <base-button
          slot="footer"
          type="default"
          data-toggle="modal"
          data-target="#leadRemoveModal"
          class="ml-md-2 mr-md-2"
          fill
          @click="leadRemoveModalVisible = true"
        >Remove lead</base-button>
      </card>
    </div>
    <div class="col-md-5">
      <card>
        <tabs :options="{ useUrlFragment: false }">
          <tab name="Notes">
            <base-alert v-if="noteError.message" type="danger">{{noteError.message}}</base-alert>
            <base-alert v-if="noteResponse.active" type="success">Note added</base-alert>
            <base-alert v-if="error.message" type="warning">{{error.message}}</base-alert>
            <notes-list
              :notes="notes"
              :element="leadInfo"
              :elementType="$store.state.elementTypes.ELEMENT_TYPE_LEAD.id"
              :noteComment="noteComment"
              :saveNoteComment="saveNoteComment"
            ></notes-list>
          </tab>
          <tab name="Updates">
            <notes-updates-list
              :notes="notes"
              :managersById="managersById"
              :noteType="$store.state.noteTypes.NOTE_TYPE_LEAD_FIELD_UPDATE.id"
              :noteTypes="noteTypes"
            ></notes-updates-list>
          </tab>
          <tab name="Tasks">
            <tasks-list
              :tasks="tasks"
              :options="{path: '/lead/', name: 'Lead'}"
              :userId="user.id"
              :getTaskData="getTaskData"
              :addTask="addTask"
              :managerGroups="managerGroups"
              :format="config.date.format"
              :tasksForm="tasksForm"
              :currentTask="currentTask"
              :closeTask="closeTask"
              :removeTask="removeTask"
            ></tasks-list>
          </tab>
        </tabs>
      </card>
    </div>
    <modal
      :show.sync="leadRemoveModalVisible"
      id="leadRemoveModal"
      :centered="false"
      :show-close="true"
      class="text-center"
    >
      <h4 class="black">
        Remove lead
        <b>{{leadInfo.name}}</b>?
      </h4>
      <button class="btn btn-default" @click="leadRemoveModalVisible = false">No</button>
      <button class="btn btn-danger" @click.prevent="removeLeads(true)">Yes</button>
    </modal>
  </div>
</template>

<script>
import { BaseAlert } from "@/components";
import { Modal } from "@/components";
import NotesList from "./Tabs/NotesList";
import NotesUpdatesList from "./Tabs/NotesUpdatesList";
import TasksList from "./Tabs/TasksList";
import Datepicker from "vuejs-datepicker";
import { main } from "./../mixins/main";
import { notes } from "./../mixins/notes";
import { leads } from "./../mixins/leads";
import { clients } from "./../mixins/clients";
import { tasks } from "./../mixins/tasks";
import authGuard from "./../guards/auth.guard";

export default {
  beforeRouteEnter: authGuard,
  components: {
    Datepicker,
    NotesList,
    TasksList,
    NotesUpdatesList,
    Modal,
    BaseAlert
  },
  data() {
    return {
      leadRemoveModalVisible: false
    };
  },
  created() {
    const { state } = this.$store;
    this.tasksForm.elementId = this.$route.params.id;
    this.tasksForm.elementType = state.elementTypes.ELEMENT_TYPE_LEAD.id;
    this.tasksFilter = {
      elementId: this.$route.params.id,
      elementType: state.elementTypes.ELEMENT_TYPE_LEAD.id
    };
    this.managersFilter = { roleId: 2 };

    this.getParams(["statuses", "countries", "currencies"]);
    this.getManagers();
    this.getClients();
    this.getProducts();
    this.getLead();
    this.getTasks();
  },
  mixins: [main, notes, leads, clients, tasks]
};
</script>
<style>
</style>
