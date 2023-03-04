import './styles/app.scss';
import Vue from 'vue';
import App from './components/App';
import './bootstrap';

new Vue({
    el: '#app',
    render: h => h(App)
});
