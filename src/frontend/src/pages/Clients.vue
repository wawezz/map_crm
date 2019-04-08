<template>
  <div v-if="user">
    <div class="row">
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
          v-if="clientsData.checked.length>0"
          data-toggle="modal"
          data-target="clientsRemove"
          type="button"
          class="btn btn-danger ml-3"
          @click="clientsRemoveModalVisible = true"
        >
          <i class="tim-icons icon-trash-simple"></i> Remove clients?
        </base-button>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <base-alert v-if="listError" type="danger">{{listError}}</base-alert>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <card title="Clients" :loading="clientsLoading">
          <div class="table">
            <active-table
              :columns="clientsColumns"
              :data="clients"
              :itemsObject="clientsData"
              :sort="clientsSort"
              :sortBy="sortClientsBy"
            ></active-table>
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
  </div>
</template>
<script>
  import { BaseAlert } from "@/components";
  import { ActiveTable } from "@/components";
  import { Pagination } from "@/components";
  import { Modal } from "@/components";
  import { main } from "./../mixins/main";
  import { clients } from "./../mixins/clients";
  import authGuard from "./../guards/auth.guard";

  export default {
    beforeRouteEnter: authGuard,
    components: {
      BaseAlert,
      ActiveTable,
      Modal,
      Pagination
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
