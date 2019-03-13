<template>
  <div v-if="user.sipId > 0 && user.sipPass">
    <div
      v-if="incomingCall"
      style=" top: calc(50% - 75px); position: fixed; z-index: 100000; background-color: #fff; width: 150px; display: flex; flex-flow: column; justify-content: center; align-items: center; left: calc(50% - 75px); border: 1px solid; padding: 20px; "
    >
      <div>Someone is calling!</div>
      <button ref="answerCall" @click.prevent="answerCall">Answer Call</button>
    </div>
    <div
      v-if="show"
      style="position: fixed;z-index: 10;bottom: 58px;right: 25px;background-color: #dedede;display: flex;padding: 10px;flex-flow: column;"
    >
      <form>
        <input placeholder="number" v-model="inputNumber">
        <input type="submit" ref="endCall" @click.prevent="makeCall" value="Call">
        <button ref="endCall" @click.prevent="hangupCall">End Call</button>
      </form>
    </div>
    <button style="position: fixed;right: 2%;bottom: 3%;z-index: 500;" @click="openClass()">call</button>
    <video ref="localAudio" loop id="video-container" style="height: 0; background-color: #000;"></video>
  </div>
</template>
<script>
import Vue from "vue";
import { mapState } from "vuex";
import "jquery-json/dist/jquery.json.min.js";
import "verto/src/jquery.verto.js";
import "verto/src/jquery.FSRTC.js";
import "verto/src/jquery.jsonrpcclient.js";

export default {
  data() {
    return {
      show: false,
      inputNumber: "",
      incomingCall: false,
      vertoHandle: null,
      vertoCallbacks: null,
      currentCall: null
    };
  },
  created() {
    (function($, _self) {
      if (_self.user.sipId > 0 && _self.user.sipPass)
        $.verto.init({}, _self.bootstrap);
    })(window.jQuery, this);

    this.vertoCallbacks = {
      onWSLogin: this.onWSLogin,
      onWSClose: this.onWSClose,
      onDialogState: this.onDialogState
    };
  },
  computed: {
    ...mapState(["user"])
  },
  methods: {
    openClass() {
      this.show = this.show ? false : true;
    },
    onWSLogin(verto, success) {
      console.log("onWSLogin", success);
    },
    onWSClose(verto, success) {
      console.log("onWSClose", success);
    },
    makeCall() {
      this.currentCall = this.vertoHandle.newCall({
        destination_number: this.inputNumber,
        caller_id_name: "Test",
        caller_id_number: "1008",
        outgoingBandwidth: "default",
        incomingBandwidth: "default",
        useStereo: true,
        useMic: "any",
        useSpeak: "any",
        dedEnc: false,
        userVariables: {
          email: "test@map-crm.lc"
        }
      });
    },
    hangupCall() {
      if (!this.currentCall) return;
      this.currentCall.hangup();
      this.currentCall = null;
      this.$refs.localAudio.pause();
      this.$refs.localAudio.srcObject = null;
      this.incomingCall = false;
    },
    answerCall() {
      this.currentCall.answer();
    },
    onDialogState(dialog) {
      console.debug("onDialogState", dialog);

      if (!this.currentCall) {
        this.currentCall = dialog;
      }

      switch (dialog.state.name) {
        case "trying":
          console.log("trying");
          break;
        case "answering":
          console.log("answering");
          this.$refs.localAudio.play();
          break;
        case "active":
          this.$refs.localAudio.play();
          console.log("active");
          break;
        case "hangup":
          this.hangupCall();
          console.log("Call ended with cause: " + dialog.cause);
          break;
        case "destroy":
          this.hangupCall();
          console.log("destroy");
          break;
        case "ringing":
          this.incomingCall = true;
          break;
      }
    },
    bootstrap(status) {
      const self = this;
      this.vertoHandle = new jQuery.verto(
        {
          login: self.user.sipId + "@91.224.12.195",
          passwd: self.user.sipPass,
          socketUrl: "wss://verto.sancom.lv:8443",
          ringFile: require("./files/ring.wav"),
          deviceParams: {
            useMic: "any",
            useSpeak: "any",
            useCamera: "none"
          },
          iceServers: true,
          tag: "video-container"
        },
        self.vertoCallbacks
      );
    }
  }
};
</script>
