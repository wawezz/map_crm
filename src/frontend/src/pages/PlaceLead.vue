<template>
  <div class="row" v-if="user && $route.params.id">
    <div class="col-md-7">
      <div>
        <base-alert v-if="access" type="danger">{{access}}</base-alert>
        <base-alert v-if="response.active" type="success">Success</base-alert>
        <base-alert v-if="error.message" type="warning">{{error.message}}</base-alert>
      </div>
      <card>
        <h5 slot="header" class="title">Place lead</h5>
        <div class="row">
          <div class="col-md-6">
            <base-input label="Place id" v-model="placeLeadsform.placeId"></base-input>
          </div>
          <div class="col-md-6">
            <base-input label="Name" v-model="placeLeadsform.name"></base-input>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <base-input label="Address" v-model="placeLeadsform.address"></base-input>
          </div>
          <div class="col-md-6">
            <base-input label="Phone" v-model="placeLeadsform.phone"></base-input>
          </div>
        </div>
        <div class="row">
          <div class="col-md-3">
            <base-input label="Website" v-model="placeLeadsform.website"></base-input>
          </div>
          <div class="col-md-3">
            <base-input label="Geo" v-model="placeLeadsform.geo"></base-input>
          </div>
          <div class="col-3">
            <label>Status</label>
            <select class="form-control form-control-line" v-model="placeLeadsform.status">
              <option v-for="status in statuses" :key="status.id" :value="status.id">{{status.name}}</option>
            </select>
          </div>
          <div class="col-md-3">
            <base-input label="Type" v-model="placeLeadsform.type"></base-input>
          </div>
        </div>
        <div class="row">
          <div class="col-3">
            <base-input
              label="Price"
              type="number"
              min="0"
              v-model="placeLeadsform.price"
              placeholder="0"
            ></base-input>
          </div>
          <div class="col-3">
            <base-input
              label="Rating"
              type="number"
              min="0"
              v-model="placeLeadsform.rating"
              placeholder="0"
            ></base-input>
          </div>
          <div class="col-3 centeredFlex">
            <base-checkbox
              label="Sync?"
              :value="placeLeadsform.toSync"
              v-model="placeLeadsform.toSync"
            ></base-checkbox>
          </div>
          <div class="col-3 centeredFlex">
            <base-checkbox
              label="Important?"
              :value="placeLeadsform.isImportant"
              v-model="placeLeadsform.isImportant"
            ></base-checkbox>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <base-input>
              <label>Review</label>
              <textarea rows="4" class="form-control" v-model="placeLeadsform.review"></textarea>
            </base-input>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <base-input>
              <label>Data</label>
              <textarea rows="4" class="form-control" v-model="placeLeadsform.data"></textarea>
            </base-input>
          </div>
        </div>
        <div class="row">
          <div class="col-6">
            <label>Date of contract</label>
            <datepicker v-model="placeLeadsform.contractAt" :format="config.date.format"></datepicker>
          </div>
          <div class="col-6">
            <label>Date of next follow up</label>
            <datepicker v-model="placeLeadsform.nextFollowupDate" :format="config.date.format"></datepicker>
          </div>
        </div>
        <base-button
          slot="footer"
          type="primary"
          class="ml-md-2 mr-md-2"
          fill
          @click.prevent="updatePlaceLead"
        >Update place lead</base-button>
        <base-button
          slot="footer"
          type="default"
          data-toggle="modal"
          data-target="#placeLeadRemoveModal"
          class="ml-md-2 mr-md-2"
          fill
          @click="placeLeadRemoveModalVisible = true"
        >Remove place lead</base-button>
      </card>
    </div>
    <div class="col-md-5">
      <card>
        <tabs :options="{ useUrlFragment: false }">
          <tab name="Map">
            <div id="map" class="map"></div>
          </tab>
          <tab name="Notes">
            <base-alert v-if="noteError.message" type="danger">{{noteError.message}}</base-alert>
            <base-alert v-if="noteResponse.active" type="success">Note added</base-alert>
            <base-alert v-if="error.message" type="warning">{{error.message}}</base-alert>
            <notes-list
              :notes="notes"
              :element="placeLeadInfo"
              :elementType="$store.state.elementTypes.ELEMENT_TYPE_PLACE_LEAD.id"
              :noteComment="noteComment"
              :saveNoteComment="saveNoteComment"
            ></notes-list>
          </tab>
          <tab name="Updates">
            <notes-updates-list
              :notes="notes"
              :managersById="managersById"
              :noteType="$store.state.noteTypes.NOTE_TYPE_PLACE_LEAD_FIELD_UPDATE.id"
              :noteTypes="noteTypes"
            ></notes-updates-list>
          </tab>
          <tab name="Tasks">
            <tasks-list
              :tasks="tasks"
              :options="{path: '/place-lead/', name: 'Place lead'}"
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
      :show.sync="placeLeadRemoveModalVisible"
      id="placeLeadRemoveModal"
      :centered="false"
      :show-close="true"
      class="text-center"
    >
      <h4 class="black">
        Remove place lead
        <b>{{placeLeadInfo.name}}</b>?
      </h4>
      <button class="btn btn-default" @click="placeLeadRemoveModalVisible = false">No</button>
      <button class="btn btn-danger" @click.prevent="removePlaceLeads(true)">Yes</button>
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
import { placeLeads } from "./../mixins/placeLeads";
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
      placeLeadRemoveModalVisible: false
    };
  },
  created() {
    const { state } = this.$store;
    this.tasksForm.elementId = this.$route.params.id;
    this.tasksForm.elementType = state.elementTypes.ELEMENT_TYPE_PLACE_LEAD.id;
    this.tasksFilter = {
      elementId: this.$route.params.id,
      elementType: state.elementTypes.ELEMENT_TYPE_PLACE_LEAD.id
    };
    this.managersFilter = { roleId: 2 };

    this.getParams(["statuses"]);
    this.getManagers();
    this.init();
    this.getTasks();
  },
  methods: {
    async init() {
      await this.getPlaceLeadByPlaceId();

      this.initMap(this.placeLeadsform.geo, this.placeLeadsform.name);
    },
    initMap(point = "POINT(0 0)", name) {
      if (point === "POINT(0 0)") return;

      const pointData = point
        .replace("POINT(", "")
        .replace(")", "")
        .split(" ");

      const myLatlng = new window.google.maps.LatLng(
        pointData[0],
        pointData[1]
      );
      const mapOptions = {
        zoom: 13,
        center: myLatlng,
        scrollwheel: false, //we disable de scroll over the map, it is a really annoing when you scroll through page
        styles: [
          {
            elementType: "geometry",
            stylers: [
              {
                color: "#1d2c4d"
              }
            ]
          }
        ]
      };
      const map = new window.google.maps.Map(
        document.getElementById("map"),
        mapOptions
      );

      const marker = new window.google.maps.Marker({
        position: myLatlng,
        title: name
      });

      // To add the marker to the map, call setMap();
      marker.setMap(map);
    }
  },
  mixins: [main, notes, placeLeads, tasks]
};
</script>
<style>
#map {
  min-height: 600px;
}
</style>
