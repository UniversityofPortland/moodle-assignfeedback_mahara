# Mahara Assignment Feedback Plugin

This feedback plugin offers purely a supporting role to its [submission sibling][1].

## Requirements

- Moodle 2.3+
- Fully integrated Moodle -> Mahara instances
- [Updated Mahara local plugin][2]
- [Mahara assignment submission plugin][1]

## Installation

Make sure your Moodle installation is fully integrated with a Mahara instance. Then you must install this
plugin one of two ways:

1. Download the source archive and extract it to the following directory: `{Moodle_Root}/mod/assign/feedback/mahara`
2. Execute the folowing command:

```
> git clone git@github.com:fellowapeman/assign-mahara-feedback.git {Moodle_Root}/mod/assign/feedback/mahara
```

The remainder of the installation can be achieved within Moodle by clicking on the _Notifications_ link.

[1]: https://github.com/fellowapeman/assign-mahara
[2]: https://github.com/fellowapeman/local-mahara
