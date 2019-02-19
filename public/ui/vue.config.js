// vue.config.js
module.exports = {
  filenameHashing: false,
  chainWebpack: (config) => {
    config.optimization.delete('splitChunks');
  },
};
