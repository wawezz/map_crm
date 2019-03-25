<template>
  <div class="row" v-if="user">
    <div class="col-sm-12 mb-3">
      <base-button
        data-toggle="modal"
        data-target="clientAdd"
        type="button"
        class="btn btn-primary"
        @click="clientAddModalVisible = true"
      >
        <i class="tim-icons icon-simple-add"></i> Add client
      </base-button>
      <base-button
        v-if="checkedClients.length>0"
        data-toggle="modal"
        data-target="clientsRemove"
        type="button"
        class="btn btn-danger ml-3"
        @click="clientsRemoveModalVisible = true"
      >
        <i class="tim-icons icon-trash-simple"></i> Remove clients?
      </base-button>
    </div>
    <div class="col-sm-12">
      <base-alert v-if="listError" type="danger">{{listError}}</base-alert>
    </div>
    <div class="col-12">
      <card title="Clients">
        <div class="table">
          <table class="table tablesorter">
            <thead>
              <tr>
                <th></th>
                <th></th>
                <th>NAME</th>
                <th>PHONE</th>
                <th>EMAIL</th>
                <th>ADDED BY</th>
                <th>RESPONSIBLE</th>
                <th>COUNTRY</th>
              </tr>
            </thead>
            <tbody v-for="listClient in clients" :key="listClient.id">
              <tr class="advance-table-row">
                <td style="width: 10px;"></td>
                <td style="width: 40px;">
                  <base-checkbox
                    :id="'checkbox_' + listClient.id"
                    :value="listClient.id"
                    v-model="checkedClients"
                  ></base-checkbox>
                </td>
                <td>
                  <router-link :to="{ path: '/client/' + listClient.id}">{{listClient.name}}</router-link>
                </td>
                <td>
                  <a href="#">{{listClient.workPhone}}</a>
                </td>
                <td>
                  <a :href="'mail-to:' + listClient.email">{{listClient.email}}</a>
                </td>
                <td>{{listClient.creatorName}}</td>
                <td>{{listClient.responsibleName}}</td>
                <td>{{listClient.countryName}}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="col-12">
          <pagination
            v-if="clientsTotalCount"
            :total="clientsTotalCount"
            :current="clientsPage"
            :size="clientsLimit"
            :prefix="'/clients/'"
          />
        </div>
        <modal
          modalClasses="modal-lg"
          :show.sync="clientAddModalVisible"
          id="clientAdd"
          :centered="false"
          :show-close="true"
        >
          <h4 class="black">Add client</h4>
          <div class="row">
            <div class="col-12">
              <base-alert v-if="error.message" type="danger">{{error.message}}</base-alert>
              <base-alert v-if="response.active" type="success">client added</base-alert>
            </div>
          </div>
          <card>
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
              class="text-center"
              fill
              @click.prevent="addClient"
            >Add client</base-button>
          </card>
        </modal>
        <modal
          :show.sync="clientsRemoveModalVisible"
          id="clientsRemove"
          :centered="false"
          :show-close="true"
          class="text-center"
        >
          <h4 class="black">Remove clients?</h4>
          <button class="btn btn-default" @click="clientsRemoveModalVisible = false">No</button>
          <button class="btn btn-danger" @click.prevent="removeClients(false)">Yes</button>
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
import { clients } from "./../mixins/clients";
import authGuard from "./../guards/auth.guard";

export default {
  beforeRouteEnter: authGuard,
  components: {
    BaseAlert,
    Modal,
    Pagination
  },
  watch: {
    $route(to, from) {
      this.getClients();
    }
  },
  data() {
    return {
      clientAddModalVisible: false,
      clientsRemoveModalVisible: false
    };
  },
  created() {
    this.managersFilter = { roleId: 2 };
    this.getParams(["countries"]);
    this.getManagers();
    this.getClients();
  },
  mixins: [main, clients]
};
</script>
<style>
</style>