import Vue from "vue";
import store from './store'

Vue.prototype.$can = function(param='') {
    if (Object.entries(store?.getters?.auth || [])?.length) {
        var permit = store?.getters?.auth?.permissionsArray || []
        return typeof param == 'string' ? permit?.includes(param) : param?.some(p=>permit?.includes(p))
    }
    return false
}

String.prototype.ucfirst = function () {
    return this.charAt(0).toUpperCase() + this.slice(1).toLowerCase();
};

/**
 * fNum format number with comma and decimal places
 * @param  {Number} decimal_places optional Number default "2"
 * @return {String} comma separated
 */
 Number.prototype.fNum = function (decimal_places=2) {
    return this.toLocaleString('en-GB', {
        minimumFractionDigits: decimal_places,
        maximumFractionDigits: decimal_places
    })
};