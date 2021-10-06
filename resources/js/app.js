
require('./bootstrap');

window.Vue = require('vue');
import VueRouter from 'vue-router'
Vue.use(VueRouter);

import Vuetify from 'vuetify'

Vue.use(Vuetify);
import 'vuetify/dist/vuetify.min.css'
//vform
import { Form, HasError, AlertError } from 'vform'
window.Form = Form;
Vue.component(HasError.name, HasError);
Vue.component(AlertError.name, AlertError);


//moment js
import moment from "moment";




import VueProgressBar from 'vue-progressbar'

Vue.use(VueProgressBar, {
    color: 'rgb(143, 255, 199)',
    failedColor: 'red',
    height: '3px'
});

import swal from 'sweetalert2';
window.swal = swal;

const toast = swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});
window.toast = toast;

window.Fire = new Vue();


let routes = [
    { path: '/dashboard', component: require('./components/Dashboard').default },
    { path: '/profile', component: require('./components/Profile').default },
    { path: '/user', component: require('./components/User').default },
    { path: '/chat', component: require('./components/Chat').default },
    { path: '/groups', component: require('./components/Groups.vue').default },
    { path: '/create-group', component: require('./components/CreateGroup.vue').default },
    { path: '/group-chat', component: require('./components/GroupChat.vue').default },
];

Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('groups', require('./components/Groups.vue').default);
Vue.component('chat', require('./components/Chat.vue').default);
Vue.component('create-group', require('./components/CreateGroup.vue').default);
Vue.component('group-chat', require('./components/GroupChat.vue').default);
Vue.component('group-list', require('./components/groupsList.vue').default);

const router = new VueRouter({
    mode :'history',
    routes // short for `routes: routes`
});


Vue.filter('mydate',function (created) {
   return moment(created).format('MMM Do YY');
});


Vue.prototype.$userId = document.querySelector("meta[name='user_id']").getAttribute('content');
const app = new Vue({
    el: '#app',
    vuetify: new Vuetify(),
    router
});
