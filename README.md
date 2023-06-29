# lytix\_timeoverview

This widget shows how participants spend their time in the course.


## Visualisation

In order too bring visual diversity a stacked bar chart is being used; also, the potentially high number of data points makes a horizontally scalable chart the better option.


## JSON

There should be an entry for every kind of Activity that makes sense to be tracked.

`TimePerStudent` is not yet implemented.  
In the future it might even be necessary to add an additional array `GradePerStudent`.

```
// TODO: use struct of arrays instead of array of structs
{
	// sorted descending by MedianTime
	Activities: [
		{
			Type: <string>, // Quiz, Video, Forum, …
			MedianTime: <number>, // share >= 0 && <= 1
			// TimePerStudent: [ <number>, … ], // sorted descending; share >= 0 && <= 1
		},…
	]
}
```

### Example

```
{
    Activities: [
        { Type: 'Forum', MedianTime: 0.15 },
        { Type: 'Course', MedianTime: 0.15 },
        { Type: 'Quiz', MedianTime: 0.2 },
        { Type: 'Video', MedianTime: 0.25 },
        { Type: 'Grade', MedianTime: 0.05 },
        { Type: 'Resource', MedianTime: 0.1 },
        { Type: 'Submission', MedianTime: 0.1 }
    ]
}
```
