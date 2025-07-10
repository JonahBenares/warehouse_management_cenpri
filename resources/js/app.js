import './bootstrap';
import { createApp } from 'vue';

import app from './components/app.vue';
import router from './router/index.js';
import { TailwindPagination } from 'laravel-vue-pagination';

// ✅ Vuetify
import 'vuetify/styles';
import { createVuetify } from 'vuetify';
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';

// ✅ vue-select
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css'; // required CSS

// ⚠️ You don't need Select2 CSS if you're using vue-select instead of jQuery Select2
// import 'select2/dist/css/select2.min.css'; ← remove this if not using jQuery Select2

const vuetify = createVuetify({
  components,
  directives,
});

const appInstance = createApp(app);

appInstance
  .use(router)
  .use(vuetify)
  .component('TailwindPagination', TailwindPagination)
  .component('v-select', vSelect) // ✅ Register vue-select globally
  .mount('#app');