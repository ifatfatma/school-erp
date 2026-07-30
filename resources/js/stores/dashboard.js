import { defineStore } from "pinia";
import { getDashboard } from "@/api/dashboard";

export const useDashboardStore = defineStore("dashboard",{

    state:()=>({

        statistics:{},

        loading:false

    }),

    actions:{

        async load(){

            this.loading=true;

            try{

                const response=await getDashboard();

                this.statistics=response.data;

            }finally{

                this.loading=false;

            }

        }

    }

});