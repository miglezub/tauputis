<template>
    <div class="container">
        <table class="table">
            <thead>
                <tr>
                    <th v-for="column in columns" :key="column" 
                        class="table-head">
                        {{ column | columnHead }}    
                    </th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="tableData.length === 0">
                    <td :colspan="columns.length +1">No data found</td>
                </tr>
                <tr v-for="(data,key1) in tableData" :key="data.id" v-else>
                    <td v-for="(value,key) in data">{{ value }}</td>
                    <td class="pl-1">
                        <button @click="deletePayment" class="btn bg-p1-red btn-sm align-left">X</button>
                    </td>
                </tr>
            </tbody>

        </table>
    </div>
</template>

<script type="text/ecmascript-6">
export default {
  props: {
    fetchUrl: { type: String, required: true },
    columns: { type: Array, required: true },
  },
  data() {
    return {
      tableData: []
    }
  },
  created() {
    return this.fetchData(this.fetchUrl)
  },
  methods: {
    fetchData(url) {
      axios.get(url)
        .then(data => {
          this.tableData = data.data.data
        })
    },
    /**
     * Get the serial number.
     * @param key
     * */
    serialNumber(key) {
      return key + 1;
    },
  },
  filters: {
    columnHead(value) {
      return value.split('_').join(' ').toUpperCase()
    }
  },
  name: 'PaymentsTable'
}
</script>

<style scoped>