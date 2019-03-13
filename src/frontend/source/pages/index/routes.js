import index from "./templates/index.vue";
import login from "./templates/login.vue";
import users from "./templates/users.vue";
import user from "./templates/user.vue";
import clients from "./templates/clients.vue";
import client from "./templates/client.vue";
import leads from "./templates/leads.vue";
import lead from "./templates/lead.vue";
import events from "./templates/events.vue";
import tasks from "./templates/tasks.vue";
import calls from "./templates/calls.vue";

export default [
  {
    name: "index",
    path: "/",
    component: index,
    meta: { title: 'Рабочий стол' }
  },
  {
    name: "login",
    path: "/login",
    component: login,
    meta: { title: 'Авторизация' }
  },
  {
    name: "users",
    path: "/users",
    component: users,
    meta: { title: 'Пользователи' }
  },
  {
    name: "user",
    path: "/user/:id",
    component: user,
    meta: { title: 'User' }
  },
  {
    name: "clients",
    path: "/clients",
    component: clients,
    meta: { title: 'Клиенты' }
  },
  {
    name: "client",
    path: "/client/:id",
    component: client,
    meta: { title: 'Клиент' }
  },
  {
    name: "leads",
    path: "/leads",
    component: leads,
    meta: { title: 'Лиды' }
  },
  {
    name: "lead",
    path: "/lead/:id",
    component: lead,
    meta: { title: 'Лид' }
  },
  {
    name: "events",
    path: "/events/:page",
    component: events,
    meta: { title: 'События' }
  },
  {
    name: "calls",
    path: "/calls/:page",
    component: calls,
    meta: { title: 'Звонки' }
  },
  {
    name: "tasks",
    path: "/tasks/:id",
    component: tasks,
    meta: { title: 'Задачи' }
  }
];
