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
    ELEMENT_TYPE_CLIENT: { id: 1, title: 'Клиент' },
    ELEMENT_TYPE_LEAD: { id: 2, title: 'Лид' },
    ELEMENT_TYPE_TASK: { id: 3, title: 'Задача' }
  },
  noteTypes: {
    NOTE_TYPE_LEAD_CREATED: { id: 1, title: 'Лид создан' },
    NOTE_TYPE_LEAD_FIELD_UPDATE: { id: 2, title: 'Отредактировано поле лида' },
    NOTE_TYPE_LEAD_STATUS_CHANGED: { id: 3, title: 'Статус лида изменен' },
    NOTE_TYPE_CLIENT_CREATED: { id: 4, title: 'Клиент создан' },
    NOTE_TYPE_CLIENT_FIELD_UPDATE: { id: 5, title: 'Отредактировано поле клиента' },
    NOTE_TYPE_COMMON: { id: 6, title: 'Обычное примечание' },
    NOTE_TYPE_CALL_IN: { id: 7, title: 'Входящий звонок' },
    NOTE_TYPE_CALL_OUT: { id: 8, title: 'Исходящий звонок' },
    NOTE_TYPE_TASK_CREATED: { id: 9, title: 'Задача создана' },
    NOTE_TYPE_TASK_RESULT: { id: 10, title: 'Результат по задаче' },
    NOTE_TYPE_SMS_IN: { id: 11, title: 'Входящее смс' },
    NOTE_TYPE_SMS_OUT: { id: 12, title: 'Исходящее смс' },
    NOTE_TYPE_SYSTEM: { id: 25, title: 'Системное сообщение' }
  },
  taskTypes: {
    TASK_TYPE_CONNECT: { value: 'connect', title: 'Связаться с клиентом' },
    TASK_TYPE_MEETING: { value: 'meeting', title: 'Встреча' }
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
