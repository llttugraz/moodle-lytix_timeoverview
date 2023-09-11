import Templates from 'core/templates';
import Widget from 'lytix_helper/widget';
import PercentRounder from 'lytix_helper/percent_rounder';

/**
 * Main function for this module.
 * @param {number} contextid
 * @param {number} courseid
 * @param {string} containerid
 */
export const init = (contextid, courseid, containerid) => {

    const WIDGET_ID = containerid ?? 'timeoverview';

    const dataPromise = Widget.getData(
        'local_lytix_lytix_timeoverview_timeoverview_get',
        {contextid: contextid, courseid: courseid}
    )
    .then(data => {
        const
            activities = data.Activities,
            length = activities.length;
        for (let i = 0; i < length; ++i) {
            if (activities[i].MedianTime > 0) {
                return data;
            }
        }
        throw new Widget.NoDataError();
    });

    // XXX: The indeces have to match!
    const stringsPromise = Widget.getStrings({
        lytix_timeoverview: { // eslint-disable-line camelcase
            differing: {
                Resource: 'timeoverview_label_resource',
                Video: 'timeoverview_label_video',
                Forum: 'timeoverview_label_forum',
                Course: 'timeoverview_label_core',
                Quiz: 'timeoverview_label_quiz',
                Grade: 'timeoverview_label_grade',
                Submission: 'timeoverview_label_submission',
                Feedback: 'timeoverview_label_feedback',
                description: 'timeoverview_description',
            },
        }
    });

    Promise.all([stringsPromise, dataPromise])
    .then(values => {
        const
            strings = values[0],
            data = values[1];

        const activities = data.Activities;
        const view = {
            description: {text: strings.description},
            data: [],
        };
        const
            length = activities.length,
            rounder = new PercentRounder();
        for (let i = 0; i < length; ++i) {
            const activity = activities[i];

            if (activity.MedianTime <= 0) {
                continue;
            }

            view.data.push({
                activity: activity.Type.toLowerCase(),
                label: strings[activity.Type],
                percent: rounder.round(activity.MedianTime * 100),
            });
        }

        return Templates.render('lytix_timeoverview/timeoverview', view);
    })
    .then(html => {
        Widget.getContentContainer(WIDGET_ID).insertAdjacentHTML('beforeend', html);
        return;
    })
    .finally(() => {
        document.getElementById(WIDGET_ID).classList.remove('loading');
    })
    .catch(error => Widget.handleError(error, WIDGET_ID));
};
