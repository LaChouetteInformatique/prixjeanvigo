/* eslint-disable no-console */
/* eslint-disable lines-around-comment */
/**
 * COMMAND LINE : --mode=?
 * ----------------------
 * grunt
 * grunt --mode=robert
 *
 * Not specifying mode will trigger 'production' mode
 * Anything else than '--mode=production' will trigger 'development' mode
 */

module.exports = (grunt) => {
  console.log("\n", "--- Grunt ---", "\n");
  console.log(grunt.option("mode"));
  const devMode = "production" !== (grunt.option("mode") || "production");
  console.log("mode = " + (devMode ? "development" : "production"));

  require("load-grunt-tasks")(grunt); // https://github.com/sindresorhus/load-grunt-tasks

  grunt.initConfig({
    pkg: grunt.file.readJSON("package.json"),

    browserify: {
      main: {
        files: [
          {
            expand: true,
            cwd: "assets/src/js/",
            src: ["*.js", "!**/*/**"], // exclude subfolders
            dest: "assets/build/",
            ext: ".js"
          }
        ],
        options: {
          browserifyOptions: { debug: devMode },
          transform: [["babelify", { presets: ["@babel/preset-env"] }]],
        },
      },
    },

    uglify: {
      my_target: {
        files: [
          {
            expand: true,
            cwd: "assets/build/",
            src: ["*.js", "!*.min.js"],
            dest: "assets/build/",
            ext: ".js"
          }
        ]
      }
    },

    "dart-sass": {
      target: {
        options: {
          sourceMap: devMode,
          outputStyle: devMode ? "expanded" : "compressed",
        },
        files: [
          {
            expand: true,
            cwd: "assets/src/scss/",
            src: ["*.scss", "!**/*/**"], // exclude subfolders
            dest: "assets/build/",
            ext: ".css"
          }
        ],
      },
    },

    // less: {
    //   development: {
    //     options: {
    //       compress: true,
    //       yuicompress: true,
    //       optimization: 2,
    //       sourceMap: true,
    //       // sourceMapFilename: "style.css.map",
    //     },
    //     files: [
    //       {
    //         expand: true,
    //         cwd: "assets/src/less/",
    //         src: ["*.less", "!**/*/**"], // exclude subfolders
    //         dest: "assets/build/",
    //         ext: ".css"
    //       }
    //     ],
    //   },
    // },

    postcss: {
      // https://github.com/postcss/postcss

      options: {
        map: devMode, // inline sourcemaps if devmode == true
        processors: () => {
          return devMode
            ? [
                // https://github.com/postcss/postcss-url
                // require("postcss-url")(), // rebase, inline or copy on url().

                // 21_09_06 : no longer work, replaced by autoprefixer plugin + cssnano
                // https://github.com/csstools/postcss-preset-env
                // require('postcss-preset-env')({
                //   autoprefixer: {} // add vendor prefixes
                // }),
                require("autoprefixer")(),
                // https://github.com/postcss/postcss-reporter
                require("postcss-reporter")(), // console.log() the messages (warnings, etc.) registered by other PostCSS plugins.
              ]
            : [
                require("autoprefixer")(),
                //require('cssnano')(), // cssnano break sourcemap
                require("postcss-reporter")(),
              ];
        },
      },

      dist: {
        src: ["assets/build/*.css","assets/build/**/*.css"],
      },
    },

    watch: {
      // https://github.com/gruntjs/grunt-contrib-watch

      options: {
        spawn: false, // 20 times faster here
      },

      scss: {
        files: ["assets/src/scss/**/*.scss", "!node-module/**"],
        tasks: ["dart-sass", "postcss"],
      },

      js: {
        files: ["assets/src/js/**/*.js", "!node-module/**"],
        tasks: ["browserify"/*"babel","uglify"*/],
      },
    },

    browserSync: {
      // https://www.browsersync.io/docs/grunt
      bsFiles: {
        src: [
          "assets/build/**/*.css",
          "assets/build/**/*.js",
          "**/*.php",
          "!node-module/**",
        ],
      },
      options: { // Not working
        proxy: {
          target: "http://appserver" //http://appserver_nginx
        },
        socket: {
          domain: 'https://bs.wp_prixjeanvigo.lndo.site', // The node proxy domain you defined in .lando.yaml. Must be https?
          port: 3000 // NOT the 3000 you might expect.
        },
        open: false,
        // logLevel: "debug",
        // logConnections: true,
        watchTask: true,
      },
    },
  });

  // grunt.registerTask('default', ['img', 'fonts', 'security']);
  if (devMode) {
    grunt.registerTask("default", [
      "dart-sass",
      /*"less",*/
      "postcss",
      "browserify",
      // "babel",
      // "browserSync",
      "watch",
    ]);
  } else {
    grunt.registerTask("default", ["dart-sass", /*"less",*/ "postcss", "browserify", /*"babel",*/"uglify"]);
  }
};
