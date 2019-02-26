<template>
  <div class="dashboard">
    <el-row class="selector">
      <el-col class="box-init">
        <h4>Ver por estado:</h4>
        <el-select
          class="select"
          v-model="state"
          placeholder="Nacional"
          @change="handleSelectChange"
        >
          <el-option
            v-for="item in states"
            :key="item.value"
            :label="item.label"
            :value="item.value"
          ></el-option>
        </el-select>
      </el-col>
      <el-col class="boxed">
        <div class="total">
          <h1>{{missing_total}}</h1>
          <p>Total de personas desaparecidas</p>
        </div>
      </el-col>
      <el-col class="boxed">
        <div class="woman">
          <h1>{{woman_total}}</h1>
          <p>Total de
            <br>mujeres
          </p>
        </div>
      </el-col>
      <el-col class="boxed">
        <div class="men">
          <h1>{{man_total}}</h1>
          <p>Total de
            <br>hombres
          </p>
        </div>
      </el-col>
      <el-col class="boxed">
        <div class="fosas">
          <h1>{{fosasLabel}}</h1>
          <p>Total de fosas
            <br>encontradas
          </p>
        </div>
      </el-col>
      <el-col class="boxed">
        <div class="fosas">
          <h1>{{ bodyLabel }}</h1>
          <p>Total de cuerpos
            <br>encontrados
          </p>
        </div>
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
      states: [],
      missing_total: 0,
      woman_total: 0,
      man_total: 0,
      fosas_total: 0,
      bodies_total: 0,
      chartData: null,
      options: {
        responsive: true,
        maintainAspectRatio: false,
      },
    };
  },
  computed: {
    fosasLabel() {
      return this.fosas_total ? this.fosas_total : 'N/A';
    },
    bodyLabel() {
      return this.bodies_total ? this.bodies_total : 'N/A';
    },
  },
  mounted() {
    this.getStates();
    this.getGlobalData();
  },
  methods: {
    getGlobalData() {
      let apiUrl = '';
      if (this.state) {
        apiUrl = `${this.siteUrl}/wp-json/charts/v1/global/${this.state}`;
      } else {
        apiUrl = `${this.siteUrl}/wp-json/charts/v1/global`;
      }
      axios(apiUrl)
        .then((response) => {
          if (response.status === 200) {
            const chart = Object.entries(response.data.chart);
            const labels = chart.map(item => item[0]);
            this.missing_total = response.data.total;
            this.woman_total = response.data.woman_total;
            this.man_total = response.data.men_total;
            this.fosas_total = response.data.fosas;
            this.bodies_total = response.data.bodies;
            const datasets = [
              {
                label: 'Mujeres',
                data: chart.map(item => item[1].woman),
                backgroundColor: 'rgb(255, 215, 60)',
                borderColor: 'rgb(255, 215, 60)',
                fill: false,
              },
              {
                label: 'Hombres',
                data: chart.map(item => item[1].man),
                backgroundColor: 'rgb(160, 195, 205)',
                borderColor: 'rgb(160, 195, 205)',
                fill: false,
              },
              {
                label: 'Total',
                data: chart.map(item => item[1].total),
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
    getStates() {
      const apiUrl = `${this.siteUrl}/wp-json/wp/v2/estado`;
      axios(apiUrl, {
        params: {
          per_page: 100,
        },
      })
        .then((response) => {
          if (response.status === 200) {
            const states = response.data.map(item => ({
              value: item.id,
              label: item.name,
            }));

            this.states = [
              {
                value: -1,
                label: 'Nacional',
              },
              ...states,
            ];
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
      console.log('this.state :', this.state);
      if (this.state === -1) {
        this.state = null;
      } else {
        this.getStateData();
      }
      this.getGlobalData();
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
  border-top: 1px solid rgba(0, 0, 0, 0.1);
  border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}
.select {
  margin-bottom: 20px;
  margin-right: 20px;
}
.boxed {
  border-left: 1px solid rgba(0, 0, 0, 0.1);
  padding: 1rem;
  text-align: center;
  h1 {
    margin: 0;
    color: #7d7272;
    font-weight: bold;
  }
  p {
    margin: 0;
  }
  .total {
    h1 {
      color: #fe503b;
    }
  }
  .fosas {
    h1 {
      color: #f71659;
    }
  }
}
h4 {
  margin-bottom: 10px;
}
@media only screen and (max-width: 767px) {
  .selector {
    flex-direction: column;
    .box-init {
      text-align: center;
    }
  }
}
</style>
