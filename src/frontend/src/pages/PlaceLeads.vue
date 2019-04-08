<template>
  <div v-if="user">
    <div class="row">
      <div class="col-sm-10 listSearch">
        <div class="form-group">
          <input
            aria-describedby="addon-right addon-left"
            class="form-control"
            ref="search"
            placeholder="name / zip code / phone"
            v-model="placeLeadsSearchString"
            v-on:keyup.enter="searchPlaceLeads"
          >
          <base-button type="button" class="btn btn-primary" @click="searchPlaceLeads">Search</base-button>
        </div>
      </div>
      <div class="col-sm-1 listFilter">
        <a
          href="#"
          data-toggle="modal"
          data-target="placeLeadsFilter"
          class="filterBtn"
          @click.prevent="placeLeadsFilterModalVisible = true"
        >
          <i class="fas fa-filter"></i>
        </a>
      </div>
      <div class="col-sm-1 listActions">
        <nav
          class="navbar navbar-expand-lg navbar-absolute"
          :class="{'bg-white': showAction, 'navbar-transparent': !showAction}"
        >
          <div class="container-fluid">
            <collapse-transition>
              <div class="collapse navbar-collapse show" v-show="showAction">
                <ul class="navbar-nav" :class="$rtl.isRTL ? 'mr-auto' : 'ml-auto'">
                  <base-dropdown
                    tag="li"
                    :menu-on-right="!$rtl.isRTL"
                    title-tag="a"
                    class="nav-item"
                    menu-classes="dropdown-navbar"
                  >
                    <a
                      slot="title"
                      href="#"
                      class="dropdown-toggle nav-link"
                      data-toggle="dropdown"
                      aria-expanded="true"
                      @click.prevent
                    >
                      <i class="tim-icons icon-settings-gear-63"></i>
                    </a>
                    <li class="nav-link">
                      <a
                        href="#"
                        data-toggle="modal"
                        data-target="placeLeadAdd"
                        class="nav-item dropdown-item"
                        @click.prevent="placeLeadAddModalVisible = true"
                      >
                        <i class="tim-icons icon-simple-add"></i> Add place lead
                      </a>
                    </li>
                    <li class="nav-link" v-if="placeLeadsData.checked.length>0">
                      <a
                        href="#"
                        data-toggle="modal"
                        data-target="placeLeadsRemove"
                        class="nav-item dropdown-item"
                        @click.prevent="placeLeadsRemoveModalVisible = true"
                      >
                        <i class="tim-icons icon-trash-simple"></i> Remove place leads?
                      </a>
                      <a
                        v-if="!multiUpdatePlaceLeads"
                        href="#"
                        data-toggle="modal"
                        data-target="placeLeadsRemove"
                        class="nav-item dropdown-item"
                        @click.prevent="multiUpdatePlaceLeads = true"
                      >Edit selected leads?</a>
                      <span v-if="multiUpdatePlaceLeads">
                        <a
                          href="#"
                          data-toggle="modal"
                          data-target="placeLeadsRemove"
                          class="nav-item dropdown-item"
                          @click.prevent="massUpdate"
                        >Update selected leads</a>
                        <a
                          href="#"
                          data-toggle="modal"
                          data-target="placeLeadsRemove"
                          class="nav-item dropdown-item"
                          @click.prevent="multiUpdatePlaceLeads = false"
                        >Cancel update</a>
                      </span>
                    </li>
                  </base-dropdown>
                </ul>
              </div>
            </collapse-transition>
          </div>
        </nav>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <base-alert v-if="listError" type="danger">{{listError}}</base-alert>
      </div>
    </div>
    <div class="row">
      <div class="col-12 mt-3">
        <card title="Place leads" :loading="placeLeadsLoading">
          <div class="table">
            <active-table
              :columns="placeLeadsColumns"
              :data="placeLeadsOriginal"
              :itemsObject="placeLeadsData"
              :updatebleData="placeLeads"
              :sort="placeLeadsSort"
              :sortBy="sortPlaceLeadsBy"
              :update="multiUpdatePlaceLeads"
              :updatebleList="{'status': statuses}"
              :updatebleStatuses="updatebleStatuses"
            ></active-table>
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
                  <base-input
                    label="Name"
                    v-model="placeLeadsform.name"
                    placeholder="New place lead"
                  ></base-input>
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
                <div class="col-2 centeredFlex">
                  <base-checkbox
                    label="Important?"
                    :value="placeLeadsform.isImportant"
                    v-model="placeLeadsform.isImportant"
                  ></base-checkbox>
                </div>
                <div class="col-2 centeredFlex">
                  <base-checkbox
                    label="Sync?"
                    :value="placeLeadsform.toSync"
                    v-model="placeLeadsform.toSync"
                  ></base-checkbox>
                </div>
                <div class="col-2">
                  <label>RN</label>
                  <select class="form-control form-control-line" v-model="placeLeadsform.rn">
                    <option value="0">0</option>
                    <option value="1">1</option>
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="col-3">
                  <base-input
                    label="Zip code"
                    type="number"
                    min="0"
                    v-model="placeLeadsform.zipCode"
                    placeholder="0"
                  ></base-input>
                </div>
                <div class="col-3">
                  <base-input label="City" v-model="placeLeadsform.city"></base-input>
                </div>
                <div class="col-3">
                  <base-input
                    label="Alexa rank"
                    type="number"
                    min="0"
                    v-model="placeLeadsform.alexaRank"
                    placeholder="0"
                  ></base-input>
                </div>
                <div class="col-3">
                  <base-input label="Online since" v-model="placeLeadsform.onlineSince"></base-input>
                </div>
              </div>
              <div class="row">
                <div class="col-3">
                  <base-input
                    label="YP reviews"
                    type="number"
                    min="0"
                    v-model="placeLeadsform.ypReviews"
                    placeholder="0"
                  ></base-input>
                </div>
                <div class="col-3">
                  <base-input
                    label="Multi location"
                    type="number"
                    min="0"
                    v-model="placeLeadsform.multiLocation"
                    placeholder="0"
                  ></base-input>
                </div>
                <div class="col-3">
                  <base-input label="Last remark" v-model="placeLeadsform.lastRemark"></base-input>
                </div>
                <div class="col-3">
                  <base-input
                    label="BBB rating"
                    type="number"
                    min="0"
                    v-model="placeLeadsform.bbbRating"
                    placeholder="0"
                  ></base-input>
                </div>
              </div>
              <div class="row">
                <div class="col-3">
                  <base-input
                    label="YP rating"
                    type="number"
                    min="0"
                    v-model="placeLeadsform.ypRating"
                    placeholder="0"
                  ></base-input>
                </div>
                <div class="col-3">
                  <base-input
                    label="Data score"
                    type="number"
                    min="0"
                    v-model="placeLeadsform.dataScore"
                    placeholder="0"
                  ></base-input>
                </div>
                <div class="col-3">
                  <base-input label="Carrier" v-model="placeLeadsform.carrier"></base-input>
                </div>
                <div class="col-3">
                  <base-input label="Caller id name" v-model="placeLeadsform.callerIdName"></base-input>
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
                  <datepicker
                    v-model="placeLeadsform.nextFollowupDate"
                    :format="config.date.format"
                  ></datepicker>
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
          <modal
            modalClasses="modal-lg"
            :show.sync="placeLeadsFilterModalVisible"
            id="placeLeadsFilter"
            :centered="false"
            :show-close="true"
          >
            <h4 class="black">Filter</h4>
            <card>
              <div class="row">
                <div class="col-4">
                  <base-input label="Carrier" v-model="placeLeadsFilterObject.carrier.value"></base-input>
                </div>

                <div class="col-2">
                  <label>Rn</label>
                  <select
                    class="form-control form-control-line"
                    v-model="placeLeadsFilterObject.rn.value"
                  >
                    <option value></option>
                    <option value="0">0</option>
                    <option value="1">1</option>
                  </select>
                </div>
                <div class="col-3">
                  <label>Caller name</label>
                  <select
                    class="form-control form-control-line"
                    v-model="placeLeadsFilterObject.callerIdName.value"
                  >
                    <option value></option>
                    <option value="1">yes</option>
                    <option value="0">no</option>
                  </select>
                </div>
                <div class="col-3">
                  <label>Data Score</label>
                  <select
                    class="form-control form-control-line"
                    v-model="placeLeadsFilterObject.dataScore.value"
                  >
                    <option value></option>
                    <option v-for="n in 10" :key="n" :value="n">{{n}}</option>
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="col-3">
                  <base-input label="Type" v-model="placeLeadsFilterObject.type.value"></base-input>
                </div>
                <div class="col-3">
                  <base-input label="City" v-model="placeLeadsFilterObject.city.value"></base-input>
                </div>
                <div class="col-3">
                  <label>Status</label>
                  <select
                    class="form-control form-control-line"
                    v-model="placeLeadsFilterObject.status.value"
                  >
                    <option value></option>
                    <option
                      v-for="status in statuses"
                      :key="status.id"
                      :value="status.id"
                    >{{status.name}}</option>
                  </select>
                </div>
                <div class="col-3 v-center">
                  <base-checkbox
                    label="Has multi location"
                    v-model="placeLeadsFilterObject.multiLocation.value"
                  ></base-checkbox>
                </div>
              </div>
              <div class="row">
                <div class="col-3">
                  <base-checkbox
                    label="Is BBB accredited"
                    v-model="placeLeadsFilterObject.bbbRating.value"
                  ></base-checkbox>
                </div>
                <div class="col-3">
                  <base-checkbox
                    label="Has YP rating"
                    v-model="placeLeadsFilterObject.ypRating.value"
                  ></base-checkbox>
                </div>
                <div class="col-3">
                  <base-checkbox
                    label="Has Google rating"
                    v-model="placeLeadsFilterObject.rating.value"
                  ></base-checkbox>
                </div>
                <div class="col-3">
                  <base-checkbox
                    label="Has Alexa rank"
                    v-model="placeLeadsFilterObject.alexaRank.value"
                  ></base-checkbox>
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <h4 class="text-center mt-3 mb-3">
                    Last update date
                    <span
                      class="clear"
                      v-if="placeLeadsFilterObject.updatedAt.to && placeLeadsFilterObject.updatedAt.from"
                      @click="getDateRange('clear')"
                    >clear</span>
                  </h4>
                  <div class="row dateList">
                    <div class="col-3">
                      <ul>
                        <li @click="getDateRange('today')">Today</li>
                        <li @click="getDateRange('this week')">This week</li>
                        <li @click="getDateRange('this month')">This month</li>
                        <li @click="getDateRange('this year')">This year</li>
                      </ul>
                    </div>
                    <div class="col-3">
                      <ul>
                        <li @click="getDateRange('week to date')">Week to date</li>
                        <li @click="getDateRange('month to date')">Month to date</li>
                        <li @click="getDateRange('year to date')">Year to date</li>
                        <li @click="getDateRange('yesterday')">Yesterday</li>
                      </ul>
                    </div>
                    <div class="col-3">
                      <ul>
                        <li @click="getDateRange('day before yesterday')">Day before yesterday</li>
                        <li @click="getDateRange('this day last week')">This day last week</li>
                        <li @click="getDateRange('prev week')">Prev week</li>
                        <li @click="getDateRange('prev month')">Prev month</li>
                      </ul>
                    </div>
                    <div class="col-3">
                      <ul>
                        <li @click="getDateRange('last 7 days')">Last 7 days</li>
                        <li @click="getDateRange('last 30 days')">Last 30 days</li>
                        <li @click="getDateRange('last 60 days')">Last 60 days</li>
                        <li @click="getDateRange('last 90 days')">Last 90 days</li>
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="col-6">
                  <datepicker
                    v-model="placeLeadsFilterObject.updatedAt.from"
                    :format="config.date.format"
                  ></datepicker>
                </div>
                <div class="col-6">
                  <datepicker
                    v-model="placeLeadsFilterObject.updatedAt.to"
                    :format="config.date.format"
                  ></datepicker>
                </div>
              </div>
              <div class="row">
                <div class="col-12 text-center">
                  <base-button type="button" class="btn btn-primary mt-3" @click="filter">Filter</base-button>
                </div>
              </div>
            </card>
          </modal>
        </card>
      </div>
    </div>
  </div>
</template>
<script>
  import { BaseAlert } from "@/components";
  import { ActiveTable } from "@/components";
  import { Pagination } from "@/components";
  import { Modal } from "@/components";
  import { CollapseTransition } from "vue2-transitions";
  import { main } from "./../mixins/main";
  import { placeLeads } from "./../mixins/placeLeads";
  import Datepicker from "vuejs-datepicker";
  import authGuard from "./../guards/auth.guard";

  export default {
    beforeRouteEnter: authGuard,
    components: {
      ActiveTable,
      CollapseTransition,
      BaseAlert,
      Modal,
      Datepicker,
      Pagination
    },

    data() {
      return {
        showFilter: false,
        showAction: false,
        placeLeadAddModalVisible: false,
        placeLeadsRemoveModalVisible: false,
        placeLeadsFilterModalVisible: false
      };
    },
    created() {
      this.managersFilter = { roleId: 2 };
      this.getParams(["statuses"]);
      this.getManagers();
      this.getPlaceLeads();
    },
    mounted() {
      this.$refs.search.focus();
    },
    methods: {
      filter() {
        this.placeLeadsFilterModalVisible = false;
        setTimeout(this.filterPlaceLeads, 1);
      }
    },
    mixins: [main, placeLeads]
  };
</script>
<style>
</style>
