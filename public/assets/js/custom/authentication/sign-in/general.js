"use strict";
var KTSigninGeneral = function () {
    var e, t, i; return {
        init: function () {
            e = document.querySelector("#kt_sign_in_form"),
            t = document.querySelector("#kt_sign_in_submit"),
            i = FormValidation.formValidation(e, { fields: { 
                    email: { 
                        validators: { 
                            regexp: { 
                                regexp: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
                                message: "Correo electrónico invalido" 
                            },
                            notEmpty: { message: "El correo electrónico es requerido" } 
                        }
                    },
                    password: { 
                        validators: { 
                            notEmpty: { message: "The password is required" } 
                        } 
                    } 
                },
                plugins: { 
                    trigger: new FormValidation.plugins.Trigger,
                    bootstrap: new FormValidation.plugins.Bootstrap5({ rowSelector: ".fv-row", eleInvalidClass: "", eleValidClass: "" }) 
                }
            }),
            t.addEventListener("click", (function (n) {
                t.setAttribute("data-kt-indicator", "on");
                t.disabled = !0;
                e.submit();
            }));
        }
    }
}();
KTUtil.onDOMContentLoaded((function () { KTSigninGeneral.init() }));