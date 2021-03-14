## Webpack 4 Configuration Setup 

#### Background

The webpack's configuration profile is setup to process sass/scss and typescript (ts) files.

You can find the sass files under src/sass. The folder structure is set up to use the power of partial to aid organization. Partials are imported into **child-style.scss**. The final file will output to **dist/child-style.frontend.bundle.min.js** and is already set up in functions.php to enqueue.

Typescript files can be found under src/ts. Use of folders for organization is recommended. This is a modified setup I've build for this project.

> Heads up that even though this is a different .ts extension, there is a freedom to use either vanilla javascript or typescript and babel will transpile the code just the same.

To add an additional script, edit the src/ts/index.ts file to add another line such as `require('./pages/home.ts');` The final file will output to **dist/child-scripts.frontend.bundle.min.js**.

You'll notice in package.json there are 3 scripts
 - `npm run dev`: used for local development environment
 - `npm run dev-build`: used for deploybot staging
	 - Removes watcher
 - `npm run build`: used for deploybot production
	 - Removes watcher, minifies, removes source maps

#### Disclaimer
The decision to use v4 instead of the latest v5 is purely due to availability of a mature configuration build. This may be updated in future releases.

#### Requirements

 - Node.js v6 or higher (I'm currently running v13.11.0)
 - Npm installed globally or for the project

#### Installation

 1. Clone the repository to your local working directory
 2. Open up a terminal / command prompt / powershell in the directory and run `npm install` 
	 - The command line needs to run in the same directory where **package.json** resides
	 - Wait for the installation to complete (a few minutes)
 3.  Run `npm run dev` to begin preprocessor compilation and a continous watcher for script changes