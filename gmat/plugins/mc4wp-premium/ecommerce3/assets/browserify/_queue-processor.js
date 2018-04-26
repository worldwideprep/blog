'use strict';

var m = require('mithril');
var qp = {};
var i18n = mc4wp_ecommerce.i18n;
var state = {
   working: false,
   done: false
};

function process(e) {
    e && e.preventDefault();

    state.working = true;
    state.done = false;

    m.request({
      method: "POST",
      url: ajaxurl + "?action=" + "mc4wp_ecommerce_process_queue",
    }).then(function(result) {
       state.done = true;
       state.working = false;
    }).catch(function(e) {
       console.log(e);
    })
}

qp.view = function() {
    return m('form', {
        method: "POST",
        onsubmit: process,
    }, [
       m('p', [
           m( 'input', {
               type: "submit",
               className: "button",
               value: state.done ? i18n.done : i18n.process,
               disabled: state.working || state.done
           }),
           state.working ? [ ' ', m('span.mc4wp-loader'), ' ', m('span.help', i18n.processing )] : ''
       ])
    ]);
};

module.exports = qp;
