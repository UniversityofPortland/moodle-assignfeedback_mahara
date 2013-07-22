# Mahara Assignment Feedback Plugin

This feedback plugin offers a purely supporting role to its [submission sibling][1].

## Requirements

- Moodle 2.3+
- [Fully integrated Moodle -> Mahara instances][3]
- [Updated Mahara local plugin][2]
- [Mahara assignment submission plugin][1]

## Installation

Make sure your Moodle installation is fully integrated with a Mahara instance. Then you must install this
plugin in one of two ways:

1. Download the source archive and extract it to the following directory: `{Moodle_Root}/mod/assign/feedback/mahara`
2. Execute the folowing command:

```
> git clone git@github.com:fellowapeman/assign-mahara-feedback.git {Moodle_Root}/mod/assign/feedback/mahara
```

The remainder of the installation can be achieved within Moodle by clicking on the _Notifications_ link.

[1]: https://github.com/fellowapeman/assign-mahara
[2]: https://github.com/fellowapeman/local-mahara
[3]: http://manual.mahara.org/en/1.5/mahoodle/mahoodle.html

## License

The Moodle assign-mahara-feedback plugin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

The Moodle assign-mahara-feedback plugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

For a copy of the GNU General Public License see http://www.gnu.org/licenses/.

## Credits

Developed for the University of Portland by Philip Cali and Tony Box (box@up.edu).

The original Moodle 1.9 version of these plugins were funded through a grant from the New Hampshire Department of Education to a collaborative group of the following New Hampshire school districts:

- Exeter Region Cooperative
- Windham
- Oyster River
- Farmington
- Newmarket
- Timberlane School District
  
The upgrade to Moodle 2.0 and 2.1 was written by Aaron Wells at Catalyst IT, and supported by:

- NetSpot
- Pukunui Technology
