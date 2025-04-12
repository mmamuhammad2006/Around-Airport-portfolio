<template>
    <div>
    <VueMultiselectComponent
      v-model="selected"
      :options="options"
    />
        <!-- Hidden input to include the selected airports in form submission -->
    <input type="hidden" name="airport_ids" :value="serializedSelected">
    </div>
</template>
<script>
import VueMultiselectComponent from 'vue-multiselect-component'
import axios from 'axios'
export default {
  components: {
    VueMultiselectComponent
  },
  data() {
    return {
      selected: [],
      options: [],
      currentPage: 1,
      totalPages: null
    }
  },
  computed:{
    serializedSelected() {
        return JSON.stringify(this.selected)
    }
  },
  methods: {
    async getAirports(page,initialLoad=false){
        try{
            const airports = await axios.get('/api/get-airports',{params:{page}})
            const formatted = airports.data.data.map(airport=>({
                ...airport,
                label: `${airport.name} - ${airport.code}`
            }));
            if(initialLoad){ this.options = formatted }
            else{
                const newOptions = formatted.filter(
                    newAirport => !this.options.some(existing=> existing.id === newAirport.id)
                )
                this.options = [...this.options, ...newOptions]
            }
            this.currentPage = airports.data.current_page
            this.totalPages = airports.data.last_page
        }catch(e){
            console.error("getting error while fetching airports: eror:- ", e)
        }
    },

    async autoFetchAirports(){
        let intervalId = setInterval(()=>{
            if(this.currentPage < this.totalPages) this.getAirports(this.currentPage + 1)
            else clearInterval(intervalId)
        },500)
    }
  },
  async mounted() {
    await this.getAirports(1,true)
    this.autoFetchAirports()
  }

}
</script>
