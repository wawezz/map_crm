<template>
  <div class="row" v-if="user">
    <div class="col-sm-12 mb-3">
      <base-button
        data-toggle="modal"
        data-target="placeLeadAdd"
        type="button"
        class="btn btn-primary"
        @click="placeLeadAddModalVisible = true"
      >
        <i class="tim-icons icon-simple-add"></i> Add place lead
      </base-button>
      <base-button
        v-if="checkedPlaceLeads.length>0"
        data-toggle="modal"
        data-target="placeLeadsRemove"
        type="button"
        class="btn btn-danger ml-3"
        @click="placeLeadsRemoveModalVisible = true"
      >
        <i class="tim-icons icon-trash-simple"></i> Remove place leads?
      </base-button>
    </div>
    <div class="col-sm-12">
      <base-alert v-if="listError" type="danger">{{listError}}</base-alert>
    </div>
    <div class="col-12">
      <card title="Place leads">
        <div class="table">
          <table class="table tablesorter">
            <thead>
              <tr>
                <th></th>
                <th></th>
                <th>NAME</th>
                <th>DATE OF CREATE</th>
                <th>TYPE</th>
                <th>STATUS</th>
                <th>PHONE</th>
              </tr>
            </thead>
            <tbody v-for="listPlaceLead in placeLeads" :key="listPlaceLead.id">
              <tr class="advance-table-row">
                <td style="width: 10px;"></td>
                <td style="width: 40px;">
                  <base-checkbox
                    :id="'checkbox_' + listPlaceLead.id"
                    :value="listPlaceLead.id"
                    v-model="checkedPlaceLeads"
                  ></base-checkbox>
                </td>
                <td>
                  <router-link
                    :to="{ path: '/place-lead/' + listPlaceLead.id}"
                  >{{listPlaceLead.name}}</router-link>
                </td>
                <td>{{listPlaceLead.createdAt.date | date}}</td>
                <td>{{listPlaceLead.type}}</td>
                <td>{{listPlaceLead.statusName}}</td>
                <td>{{listPlaceLead.phone}}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="col-12">
          <pagination
            v-if="placeLeadsTotalCount"
            :total="placeLeadsTotalCount"
            :current="placeLeadsPage"
            :size="placeLeadsLimit"
            :prefix="'/place-leads/'"
          />
        </div>
        <modal
          modalClasses="modal-lg"
          :show.sync="placeLeadAddModalVisible"
          id="placeLeadAdd"
          :centered="false"
          :show-close="true"
        >
          <h4 class="black">Add place lead</h4>
          <div class="row">
            <div class="col-12">
              <base-alert v-if="error.message" type="danger">{{error.message}}</base-alert>
              <base-alert v-if="response.active" type="success">place lead added</base-alert>
            </div>
          </div>
          <card>
            <div class="row">
              <div class="col-4">
                <base-input label="Place id" v-model="placeLeadsform.placeId"></base-input>
              </div>

              <div class="col-5">
                <base-input label="Name" v-model="placeLeadsform.name" placeholder="New place lead"></base-input>
              </div>
              <div class="col-3">
                <label>Status</label>
                <select class="form-control form-control-line" v-model="placeLeadsform.status">
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
                <base-input label="Phone" v-model="placeLeadsform.phone"></base-input>
              </div>
              <div class="col-5">
                <base-input label="Address" v-model="placeLeadsform.address"></base-input>
              </div>
              <div class="col-3">
                <base-input label="Type" v-model="placeLeadsform.type"></base-input>
              </div>
            </div>
            <div class="row">
              <div class="col-4">
                <base-input label="Website" v-model="placeLeadsform.website"></base-input>
              </div>
              <div class="col-5">
                <base-input label="Geo" v-model="placeLeadsform.geo"></base-input>
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
                  label="Campaign code"
                  type="number"
                  min="0"
                  v-model="placeLeadsform.campaignCode"
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
              class="text-center"
              fill
              @click.prevent="addPlaceLead"
            >Add place lead</base-button>
          </card>
        </modal>
        <modal
          :show.sync="placeLeadsRemoveModalVisible"
          id="placeLeadsRemove"
          :centered="false"
          :show-close="true"
          class="text-center"
        >
          <h4 class="black">Remove place leads?</h4>
          <button class="btn btn-default" @click="placeLeadsRemoveModalVisible = false">No</button>
          <button class="btn btn-danger" @click.prevent="removePlaceLeads(false)">Yes</button>
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
import { placeLeads } from "./../mixins/placeLeads";
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
      this.getPlaceLeads();
    }
  },
  data() {
    return {
      placeLeadAddModalVisible: false,
      placeLeadsRemoveModalVisible: false
    };
  },
  created() {
    this.managersFilter = { roleId: 2 };
    this.getParams(["statuses"]);
    this.getManagers();
    this.getPlaceLeads();
  },
  mixins: [main, placeLeads]
};
</script>
<style>
</style>
