# heatmap.kvv.guru

A quick overview of the traffic density of tram and bus routes in the city of Karlsruhe

## Setup

The web application has been successfully tested with the following setup (older software versions may also work):

* nginx >= 1.18
* PHP >= 8.3

You can run your own instance by following the steps below:

1. Move at least the contents of the `public` folder to your web server and set it as your document root.
2. Create a `data` folder next to the `public` folder (if it does not already exist).
3. Download one or more GTFS archives from https://www.kvv.de/fahrplan/fahrplaene/open-data.html
4. Extract the ZIP file and move its contents to a subfolder of `data` with a name of your choice (e.g. the year or the full date of the download).
5. Navigate your web browser to the address you defined for your copy of the web application.

The very first page load may take some time because large CSV files must be processed initially.
