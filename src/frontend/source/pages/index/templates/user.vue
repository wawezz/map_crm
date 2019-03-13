<template>
  <div v-if="user">
        <div id="page-wrapper" class="p-t-20">
          
            <div class="row" v-if="access">
              <div class="col-12">
                <div class="white-box">
                  <div class="alert alert-danger">{{access}}</div>
                </div>
              </div>
            </div>
            <div class="row">
                    <div class="col-md-4 col-xs-12">
                        <div class="white-box">
                            <div class="user-bg"> <img width="100%" alt="user" src="./images/avatar_bg.jpg">
                                <div class="overlay-box" :class="userInfo.isOnline > 1?' online':userInfo.isOnline==1?' hold':''">
                                    <div class="user-content">
                                        <img :src='userInfo.avatarId>0?userInfo.avatar:require("./images/avatar.png")' class="thumb-lg img-circle" alt="img">
                                        <h4 class="text-white">{{userInfo.name}}</h4>
                                        <h5 class="text-white">{{userInfo.email}}</h5> 
                                        <router-link v-if="user.roleId == 1" :to="'/tasks/' + userInfo.id" class="text-white"><b>Задачи пользователя</b></router-link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8 col-xs-12">
                        <div class="white-box">
                            <ul class="nav nav-tabs tabs customtab">
                                <li class="active tab">
                                    <a href="#profile" data-toggle="tab"> <span class="visible-xs"><i class="fa fa-user"></i></span> <span class="hidden-xs">Профиль</span> </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="profile">
                                    <form class="form-horizontal form-material">
                                        <div class="alert alert-success" v-if="response.active">Данные обновлены</div>
                                        <div class="alert alert-danger" v-if="error.message">{{error.message}}</div>
                                        <div class="form-group">
                                            <label class="col-md-12">Полное имя</label>
                                            <div class="col-md-12">
                                                <input type="text" v-model="usersForm.name" :placeholder='userInfo.name' class="form-control form-control-line"> </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="example-email" class="col-md-12">Email</label>
                                            <div class="col-md-12">
                                                <input type="email" v-model="usersForm.email" :placeholder='userInfo.email' class="form-control form-control-line" name="example-email" id="example-email"> </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-12">Пароль</label>
                                            <div class="col-md-12">
                                                <input type="password" v-model="usersForm.password" class="form-control form-control-line"> </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-12">Фотография</label>
                                            <div class="col-sm-12">
                                                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                                    <div class="form-control" data-trigger="fileinput"> 
                                                        <i v-if="usersForm.image" class="glyphicon glyphicon-file"></i> 
                                                        <span class="fileinput-filename">{{(usersForm.image)?usersForm.image.name:''}}</span>
                                                    </div>
                                                    <span class="input-group-addon btn btn-default btn-file"> 
                                                        <span class="fileinput-new">Выберете файл</span>
                                                        <input type="hidden">
                                                        <input type="file" id="avatar" ref="avatar" v-on:change="handleFileUpload()"/>
                                                    </span>
                                                    <a href="#" @click.prevent="removeFile()" v-if="usersForm.image" class="input-group-addon btn btn-default" data-dismiss="fileinput">Удалить</a> 
                                                </div>
                                            </div>
                                        </div>
                                        <span v-if="user.roleId==1">
                                          <div class="form-group">
                                              <label class="col-sm-12">Выберете роль</label>
                                              <div class="col-sm-12">
                                                  <select class="form-control form-control-line" @change="updateGroup()" v-model="usersForm.roleId">
                                                      <option v-for="role in params.roles" :key="role.id" :value="role.id">{{role.name}}</option>
                                                  </select>
                                              </div>
                                          </div>
                                          <span v-if="usersForm.roleId != 1">
                                            <div class="form-group">
                                              <label class="col-sm-12">Выберете Группу</label>
                                              <div class="col-sm-12">
                                                  <select class="form-control form-control-line" v-model="usersForm.groupId">
                                                      <option v-for="group in params.groups" :key="group.id" :value="group.id">{{group.name}}</option>
                                                  </select>
                                              </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="example-email" class="col-md-12">SIP id</label>
                                                <div class="col-md-12">
                                                    <input type="text" v-model="usersForm.sipId" class="form-control form-control-line"> </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-12">SIP password</label>
                                                <div class="col-md-12">
                                                    <input type="text" v-model="usersForm.sipPass" class="form-control form-control-line"> </div>
                                            </div>
                                          </span>
                                        </span>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <button class="btn btn-success fRigh" @click.prevent="updateProfile()">Обновить профиль</button>
                                                <div v-if="usersForm.id != user.id" class="btn btn-danger" data-toggle="modal" data-target=".bs-profileRemove-modal-md">Удалить профиль</div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade bs-profileRemove-modal-md" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ref="usersRemoveClose">×</button>
                                <h4 class="modal-title" id="mySmallModalLabel">Удалить пользователя <b>{{userInfo.name}}</b>?</h4> </div>
                            <div class="modal-body">
                              <button class="btn btn-success button" data-dismiss="modal" aria-hidden="true">Нет</button>
                              <button class="btn btn-danger" @click.prevent="removeUsers(true)">Да</button>
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
import { users } from "./mixins/users";
import authGuard from "./../components/guards/auth.guard";

export default {
  beforeRouteEnter: authGuard,    
  data() {
    return {
      data: {}
    };
  },
  watch: {
    $route(to, from) {
      this.initUser();
    }
  },
  created() {
    this.getParams();
    this.initUser();
  },
  mounted() {
  },
  methods: {
    handleFileUpload() {
      this.usersForm.image = this.$refs.avatar.files[0];
    },
    removeFile() {
      this.usersForm.image = null;
    },
    updateGroup() {
      if (this.usersForm.roleId != 1 && this.usersForm.groupId == 0)
        this.usersForm.groupId = 1;
    }
  },
  mixins: [main, users],
  components: {
  }
};
</script>

<style>
.user-bg .overlay-box {
  background: #2cabe3;
  border-bottom: 36px #b90400 solid;
  opacity: 0.8;
}
.user-bg .overlay-box.online {
    border-color: #00de70;
}
.user-bg .overlay-box.hold {
    border-color: #ffd400;
}
</style>