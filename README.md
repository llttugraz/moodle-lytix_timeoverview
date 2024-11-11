# lytix\_timeoverview

This plugin is a subplugin of [local_lytix](https://github.com/llttugraz/moodle-local_lytix).
The `lytix_timeoverview` module serves as a comprehensive tool to provide a detailed view of time-related metrics in Moodle. This plugin is especially essential for capturing user interactions and activities that are not automatically logged by Moodle, particularly within the custom-developed dashboard.

## Installation

1. Download the plugin and extract the files.
2. Move the extracted folder to your `moodle/local/lytix/modules/` directory.
3. Log in as an admin in Moodle and navigate to `Site Administration > Plugins > Install plugins`.
4. Follow the on-screen instructions to complete the installation.

## Requirements

- Supported Moodle Version: 4.1 - 4.5
- Supported PHP Version:    7.4 - 8.3
- Supported Databases:      MariaDB, PostgreSQL
- Supported Moodle Themes:  Boost

This plugin has only been tested under the above system requirements against the specified versions.
However, it may work under other system requirements and versions as well.

## Features

This module provides a detailed view of time-related metrics in Moodle.

- Utilizes PHP for backend operations, ensuring smooth data storage and retrieval.
- Interactions recorded via the dashboard are stored in the backend for future access and analysis.

## Configuration

No settings for the subplugin are available.

## Usage

The provided widget of this subplugin is part of the LYTIX operation modes `Learner's Corner` and `Course Dashboard`. We refer to [local_lytix](https://github.com/llttugraz/moodle-local_lytix) for the configuration of this operation modes. If the mode `Learner's Corner` or `Course Dashboard` is active  and if a course is in the list of supported courses for this mode, then this widget is displayed when clicking on `Learner's Corner` \ `Course Dashboard` in the main course view.

## API Documentation

No API.

## Privacy

No personal data are stored.

## Dependencies

- [local_lytix](https://github.com/llttugraz/moodle-local_lytix).
- [lytix_helper](https://github.com/llttugraz/moodle-lytix_helper).
- [lytix_logs](https://github.com/llttugraz/moodle-lytix_logs).

## License

This plugin is licensed under the [GNU GPL v3](https://github.com/llttugraz/moodle-lytix_timeoverview?tab=GPL-3.0-1-ov-file).

## Contributors

- **Günther Moser** - Developer - [GitHub](https://github.com/ghinta)
- **Alex Kremser** - Developer
