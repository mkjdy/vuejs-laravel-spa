import axios from 'axios';
import store from '../store/index'
import route from '../routes'
const api = axios.create({
	baseURL: '/api',
})

api.interceptors.request.use(function (config) {
	config.headers.Authorization =  `Bearer ${store.getters.auth.token}`
	// if (config.method == 'get') {
	// 	store.commit("SET_APP_LOADER", true)
	// }
    return config;
});

api.interceptors.response.use(
	function (response) {
		// if (response.config.method == 'get') {
		// 	store.commit("SET_APP_LOADER", false)
		// }
	  	return response;
	},

	function (error) {
		let res = error.response;

		if ([401].includes(res.status)) {
			// localStorage.clear()
			// alert(`${document.title.toUpperCase()}\n• Session expired please re-login your credential to avoid any error!`)
			// window.location.href = '/';
			localStorage.removeItem("meta");
            route.replace({ name: "Login" })
			alert(`${document.title.toUpperCase()}\n• Session expired please re-login your credential to avoid any error!`)
		} else {
			// if (error.response.config.method == 'get') {
			// 	store.commit("SET_APP_LOADER", false)
			// }
		}

	  	return Promise.reject(error);
	}
  );

  export default api