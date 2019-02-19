<template>
  <div class="dashboard">
    <el-row class="selector">
      <el-col :xs="24" :sm="24" :md="12" :lg="4" :xl="4">
        <h4>Seleccione un estado:</h4>
      </el-col>
      <el-col :xs="24" :sm="24" :md="12" :lg="20" :xl="20">
        <el-select v-model="state" placeholder="Estados" @change="handleSelectChange">
          <el-option
            v-for="item in states"
            :key="item.value"
            :label="item.label"
            :value="item.value"
          ></el-option>
        </el-select>
      </el-col>
    </el-row>
    <el-row class="chart-holder">
      <el-col :span="24">
        <line-chart :chart-data="chartData" :options="options"></line-chart>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import axios from 'axios';
import LineChart from './LineChart.vue';

export default {
  name: 'Dashboard',
  components: {
    LineChart,
  },
  props: {
    msg: String,
  },
  data() {
    return {
      siteUrl: window.ui_data.site_url,
      state: null,
      states: [{ value: 1, label: 1 }],
      chartData: null,
      options: {
        responsive: true,
        maintainAspectRatio: false,
      },
    };
  },
  mounted() {
    this.getStates();
  },
  methods: {
    getStates() {
      const apiUrl = `${this.siteUrl}/wp-json/wp/v2/estado`;
      axios(apiUrl, {
        params: {
          per_page: 100,
        },
      })
        .then((response) => {
          if (response.status === 200) {
            this.states = response.data.map(item => ({
              value: item.id,
              label: item.name,
            }));
            this.state = this.states[0].value;
            this.getStateData();
          } else {
            /* eslint no-console: "off" */
            console.log('error loading states');
          }
        })
        .catch((error) => {
          /* eslint no-console: "off" */
          console.log('error :', error);
        });
    },
    getStateData() {
      const apiUrl = `${this.siteUrl}/wp-json/charts/v1/state/${this.state}`;
      axios(apiUrl)
        .then((response) => {
          if (response.status === 200) {
            const { data } = response;
            const labels = data.map(item => item.year);
            const datasets = [
              {
                label: 'Mujeres',
                data: data.map(item => item.woman),
                backgroundColor: 'rgb(255, 215, 60)',
                borderColor: 'rgb(255, 215, 60)',
                fill: false,
              },
              {
                label: 'Hombres',
                data: data.map(item => item.man),
                backgroundColor: 'rgb(160, 195, 205)',
                borderColor: 'rgb(160, 195, 205)',
                fill: false,
              },
              {
                label: 'Total',
                data: data.map(item => item.total),
                backgroundColor: 'rgb(254, 78, 53)',
                borderColor: 'rgb(254, 78, 53)',
                fill: false,
              },
            ];
            this.chartData = {
              labels,
              datasets,
            };
          } else {
            /* eslint no-console: "off" */
            console.log('Error en estados');
          }
        })
        .catch((error) => {
          /* eslint no-console: "off" */
          console.log('error :', error);
        });
    },
    handleSelectChange() {
      this.getStateData();
    },
  },
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
.chart-holder {
  margin-top: 1rem;
}
.selector {
  display: flex;
  align-items: center;
  justify-content: center;
}
h4 {
  margin: 0;
}
</style>
