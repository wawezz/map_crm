<template>
  <div class="row" v-if="user && $route.params.id">
    <div class="col-md-7">
      <div>
        <base-alert v-if="access" type="danger">{{access}}</base-alert>
        <base-alert v-if="response.active" type="success">Success</base-alert>
        <base-alert v-if="error.message" type="warning">{{error.message}}</base-alert>
      </div>
      <card>
        <tabs :options="{ useUrlFragment: false }">
          <tab name="Client">
            <div class="row">
              <div class="col-4">
                <base-input label="Name" placeholder="Joe Division" v-model="clientform.name"></base-input>
              </div>
              <div class="col-4">
                <base-input label="Surname" v-model="clientform.surname"></base-input>
              </div>
              <div class="col-4">
                <base-input label="Email" type="email" v-model="clientform.email"></base-input>
              </div>
            </div>
            <div class="row">
              <div class="col-4">
                <base-input label="Phone" v-model="clientform.phone"></base-input>
              </div>
              <div class="col-4">
                <base-input label="Additional phone" v-model="clientform.otherPhone"></base-input>
              </div>
              <div class="col-4">
                <label>Responsible</label>
                <select class="form-control form-control-line" v-model="clientform.responsible">
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
              <div class="col-4">
                <label>Country</label>
                <select class="form-control form-control-line" v-model="clientform.countryId">
                  <option
                    v-for="country in countries"
                    :key="country.id"
                    :value="country.id"
                  >{{country.name}}</option>
                </select>
              </div>
              <div class="col-4">
                <base-input label="State" v-model="clientform.state"></base-input>
              </div>
              <div class="col-4">
                <base-input label="City" v-model="clientform.city"></base-input>
              </div>
            </div>
            <div class="row">
              <div class="col-4">
                <base-input label="Street" v-model="clientform.street"></base-input>
              </div>

              <div class="col-4">
                <base-input label="Building" v-model="clientform.building"></base-input>
              </div>
              <div class="col-4">
                <base-input label="Apartment/Office" v-model="clientform.flat"></base-input>
              </div>
            </div>
            <div class="row">
              <div class="col-4">
                <base-input label="ZIP code" v-model="clientform.zip"></base-input>
              </div>
              <div class="col-4 centeredFlex">
                <base-checkbox
                  label="Email confirmed?"
                  :value="clientform.emailVerified"
                  v-model="clientform.emailVerified"
                ></base-checkbox>
              </div>
              <div class="col-4 centeredFlex">
                <base-checkbox
                  label="Phone confirmed?"
                  :value="clientform.phoneVerified"
                  v-model="clientform.phoneVerified"
                ></base-checkbox>
              </div>
            </div>
            <base-button
              slot="footer"
              type="primary"
              class="ml-md-2 mr-md-2"
              fill
              @click.prevent="updateClient"
            >Update client</base-button>
            <base-button
              slot="footer"
              type="default"
              data-toggle="modal"
              data-target="#clientRemoveModal"
              class="ml-md-2 mr-md-2"
              fill
              @click="clientRemoveModalVisible = true"
            >Remove client</base-button>
          </tab>
          <tab name="Leads">
            <table class="table tablesorter">
              <tbody>
                <tr v-for="lead in leads" :key="lead.id">
                  <td>
                    <router-link :to="{ path: '/lead/' + lead.id}">{{lead.name}}</router-link>
                  </td>
                  <td>
                    <span :class="'lead__status lead__status__'+lead.status">{{lead.statusName}}</span>
                  </td>
                  <td>{{lead.budget}}</td>
                </tr>
              </tbody>
            </table>
          </tab>
        </tabs>
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
              :element="clientInfo"
              :elementType="$store.state.elementTypes.ELEMENT_TYPE_CLIENT.id"
              :noteComment="noteComment"
              :saveNoteComment="saveNoteComment"
            ></notes-list>
          </tab>
          <tab name="Updates">
            <notes-updates-list
              :notes="notes"
              :managersById="managersById"
              :noteType="$store.state.noteTypes.NOTE_TYPE_CLIENT_FIELD_UPDATE.id"
              :noteTypes="noteTypes"
            ></notes-updates-list>
          </tab>
          <tab name="Tasks">
            <tasks-list
              :tasks="tasks"
              :options="{path: '/client/', name: 'Client'}"
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
      :show.sync="clientRemoveModalVisible"
      id="clientRemoveModal"
      :centered="false"
      :show-close="true"
      class="text-center"
    >
      <h4 class="black">
        Remove client
        <b>{{clientInfo.name}}</b>?
      </h4>
      <button class="btn btn-default" @click="clientRemoveModalVisible = false">No</button>
      <button class="btn btn-danger" @click.prevent="removeClients(true)">Yes</button>
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
  import { tasks } from "./../mixins/tasks";
  import { clients } from "./../mixins/clients";
  import { leads } from "./../mixins/leads";
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
        clientRemoveModalVisible: false
      };
    },
    created() {
      const { state } = this.$store;
      this.tasksForm.elementId = this.$route.params.id;
      this.tasksForm.elementType = state.elementTypes.ELEMENT_TYPE_CLIENT.id;
      this.tasksFilter = {
        elementId: this.$route.params.id,
        elementType: state.elementTypes.ELEMENT_TYPE_CLIENT.id
      };
      this.managersFilter = { roleId: 2 };
      this.clientsFilter = { client: this.$route.params.id };

      this.getParams(["countries"]);
      this.getManagers();
      this.getTasks();
      this.getClient();
      this.getLeads();
    },
    mixins: [main, notes, tasks, clients, leads]
  };
</script>
<style>
</style>
