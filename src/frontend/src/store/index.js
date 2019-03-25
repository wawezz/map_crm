import createPersistedState from "vuex-persistedstate";

export const plugins = [
  createPersistedState(),
];

export const state = {
  user: null,
  authFormData: {
    username: "",
    password: ""
  },
  elementTypes: {
    ELEMENT_TYPE_CLIENT: { id: 1, title: 'Client' },
    ELEMENT_TYPE_LEAD: { id: 2, title: 'Lead' },
    ELEMENT_TYPE_TASK: { id: 3, title: 'Task' },
    ELEMENT_TYPE_PLACE_LEAD: { id: 16, title: 'Place lead' },
  },
  noteTypes: {
    NOTE_TYPE_LEAD_CREATED: { id: 1, title: 'Lead created' },
    NOTE_TYPE_LEAD_FIELD_UPDATE: { id: 2, title: 'Lead field update' },
    NOTE_TYPE_LEAD_STATUS_CHANGED: { id: 3, title: 'Lead status update' },
    NOTE_TYPE_PLACE_LEAD_CREATED: { id: 13, title: 'Place lead created' },
    NOTE_TYPE_PLACE_LEAD_FIELD_UPDATE: { id: 14, title: 'Place lead field update' },
    NOTE_TYPE_PLACE_LEAD_STATUS_CHANGED: { id: 15, title: 'Place lead status update' },
    NOTE_TYPE_CLIENT_CREATED: { id: 4, title: 'Client created' },
    NOTE_TYPE_CLIENT_FIELD_UPDATE: { id: 5, title: 'Client field update' },
    NOTE_TYPE_COMMON: { id: 6, title: 'Common note' },
    NOTE_TYPE_CALL_IN: { id: 7, title: 'Incoming call' },
    NOTE_TYPE_CALL_OUT: { id: 8, title: 'Outgoing call' },
    NOTE_TYPE_TASK_CREATED: { id: 9, title: 'Task created' },
    NOTE_TYPE_TASK_RESULT: { id: 10, title: 'Task result' },
    NOTE_TYPE_SMS_IN: { id: 11, title: 'Incoming SMS' },
    NOTE_TYPE_SMS_OUT: { id: 12, title: 'Outgoing SMS' },
    NOTE_TYPE_SYSTEM: { id: 25, title: 'System message' }
  },
  taskTypes: {
    TASK_TYPE_CONNECT: { value: 'connect', title: 'Connect to client' },
    TASK_TYPE_MEETING: { value: 'meeting', title: 'Meeting' }
  },
};

export const mutations = {
  SET_USER(state, user) {
    state.user = user;
  },
  CLEAR_USER(state) {
    state.user = null;
  },
  CLEAR_AUTH_FORM_DATA(state) {
    state.authFormData.username = "";
    state.authFormData.password = "";
  }
};
