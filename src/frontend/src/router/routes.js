import DashboardLayout from "@/layout/dashboard/DashboardLayout.vue";
// GeneralViews
import NotFound from "@/pages/NotFoundPage.vue";

// Admin pages
const Login = () => import(/* webpackChunkName: "login" */"@/pages/Login.vue");
const Events = () => import(/* webpackChunkName: "events" */"@/pages/Events.vue");
const Users = () => import(/* webpackChunkName: "users" */"@/pages/Users.vue");
const Dashboard = () => import(/* webpackChunkName: "dashboard" */"@/pages/Dashboard.vue");
const Profile = () => import(/* webpackChunkName: "common" */ "@/pages/Profile.vue");
const Calls = () => import(/* webpackChunkName: "calls" */"@/pages/Calls.vue");
const PlaceLeads = () => import(/* webpackChunkName: "placeLeads" */ "@/pages/PlaceLeads.vue");
const PlaceLead = () => import(/* webpackChunkName: "placeLead" */ "@/pages/PlaceLead.vue");
const Leads = () => import(/* webpackChunkName: "leads" */ "@/pages/Leads.vue");
const Lead = () => import(/* webpackChunkName: "lead" */ "@/pages/Lead.vue");
const Clients = () => import(/* webpackChunkName: "clients" */ "@/pages/Clients.vue");
const Client = () => import(/* webpackChunkName: "client" */ "@/pages/Client.vue");
const Tasks = () => import(/* webpackChunkName: "tasks" */ "@/pages/Tasks.vue");

const routes = [
  {
    path: "/login",
    component: Login
  },
  {
    path: "/",
    component: DashboardLayout,
    redirect: "/dashboard",
    children: [
      {
        path: "dashboard",
        name: "dashboard",
        component: Dashboard
      },
      {
        path: "profile/:id",
        name: "profile",
        component: Profile
      },
      {
        path: "users/:page",
        name: "users",
        component: Users
      },
      {
        path: "events/:page",
        name: "events",
        component: Events
      },
      {
        path: "calls/:page",
        name: "calls",
        component: Calls
      },
      {
        path: "place-leads/:page",
        name: "place-leads",
        component: PlaceLeads
      },
      {
        path: "place-lead/:id",
        name: "place-lead",
        component: PlaceLead
      },
      {
        path: "leads/:page",
        name: "leads",
        component: Leads
      },
      {
        path: "lead/:id",
        name: "lead",
        component: Lead
      },
      {
        path: "clients/:page",
        name: "clients",
        component: Clients
      },
      {
        path: "client/:id",
        name: "client",
        component: Client
      },
      {
        path: "tasks/:id",
        name: "tasks",
        component: Tasks
      }
    ]
  },
  { path: "*", component: NotFound },
];

/**
 * Asynchronously load view (Webpack Lazy loading compatible)
 * The specified component must be inside the Views folder
 * @param  {string} name  the filename (basename) of the view to load.
function view(name) {
   var res= require('../components/Dashboard/Views/' + name + '.vue');
   return res;
};**/

export default routes;
