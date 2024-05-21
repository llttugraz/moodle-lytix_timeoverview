# lytix\_timeoverview

This plugin is a subplugin of [local_lytix](https://github.com/llttugraz/moodle-local_lytix).
The `lytix_timeoverview` module serves as a comprehensive tool to provide a detailed view of time-related metrics in Moodle. This plugin is especially essential for capturing user interactions and activities that are not automatically logged by Moodle, particularly within the custom-developed dashboard.

## Installation

1. Download the plugin and extract the files.
2. Move the extracted folder to your `moodle/local/lytix/modules/` directory.
3. Log in as an admin in Moodle and navigate to `Site Administration > Plugins > Install plugins`.
4. Follow the on-screen instructions to complete the installation.

## Requirements

- Moodle Version: 4.1+
- PHP Version: 7.4+
- Supported Databases: MariaDB, PostgreSQL
- Supported Moodle Themes: Boost

## Features

- Utilizes PHP for backend operations, ensuring smooth data storage and retrieval.
- Interactions recorded via the dashboard are stored in the backend for future access and analysis.

## Dependencies

- [local_lytix](https://github.com/llttugraz/moodle-local_lytix).
- [lytix_config](https://github.com/llttugraz/moodle-lytix_config).
- [lytix_logs](https://github.com/llttugraz/moodle-lytix_logs).

## License

This plugin is licensed under the [GNU GPL v3](https://github.com/llttugraz/moodle-lytix_timeoverview?tab=GPL-3.0-1-ov-file).

## Contributors

- **Günther Moser** - Developer - [GitHub](https://github.com/ghinta)
- **Alex Kremser** - Developer
