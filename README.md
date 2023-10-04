# lytix_timeoverview

## Overview
The `lytix_timeoverview` module serves as a comprehensive tool to provide a detailed view of time-related metrics in Moodle. This plugin is especially essential for capturing user interactions and activities that are not automatically logged by Moodle, particularly within the custom-developed dashboard.

## Features

### Backend Database Interaction
- Utilizes PHP for backend operations, ensuring smooth data storage and retrieval.
- Interactions recorded via the dashboard are stored in the backend for future access and analysis.

### Essential Database Tables
- The module integrates two crucial database tables:
    - `lytix logs logs`: Captures actions related to the custom dashboard.
    - `lytix logs aggregated logs`: Stores aggregated data of student activities.

## Usage
1. Install and activate the `lytix_timeoverview` module in your Moodle instance.
2. The plugin will start capturing specific interactions within the custom dashboard.
3. Access the recorded logs for insights into user activities and generate detailed reports as needed.

> **Note**: Ensure you have the required permissions to install, activate, and access the logs in the `lytix_timeoverview` module in your Moodle courses.
