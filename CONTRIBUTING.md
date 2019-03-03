# Contributing Guidelines for TAMUctf-probs
## Task Tracking
All tasks will be kept track of using Github's new `Projects` tab.  
There are currently four columns:  
1. Backlog: List of tasks that are planned and can be pulled from.
2. Assigned: List of tasks that are assigned to a dev but are not in progress yet.
3. In Progress: List of tasks that are currently being worked on.  
4. Needs Review: List of tasks that are currently pull requested but not merged.  
5. Done: List of tasks that have been merged.  
   
How to add a task:
1. Navigate to `Projects->TAMUctf Challenge Creation`
2. Click the `+` button on the backlog column.
3. Add an appropriate name for the task.
4. Click the down arrow and select `Convert to Issue`
5. Add any extra desciption needed and convert.
6. Navigate to the `Issues` tab and select your new issue.
7. Click the gear near `Assignees` and assign yourself.
8. Click the gear near `Labels` and add the appropriate labels.

## Challenge Submission
Please do not simply just push to the master branch.
### Pull Requests (PRs)  
- All created problems will be submitted as a pull request to the main repo (currently https://github.tamu.edu/tamuctf-dev/tamuctf-probs)
- All challenge development will be done on separate branches 
- All files needed for an individual challenge must be present before the PR (pull request) is made
- In order for a PR to be merged two things have to happen
  - It has to pass the basic Continuous Integration (CI) testing (May or may not be added)
  - One person has to approve the PR
- How to create a pull request: https://help.github.com/articles/creating-a-pull-request/
- Branching: https://git-scm.com/book/en/v2/Git-Branching-Basic-Branching-and-Merging

### Description
Put the challenge name and description you would like to see during the CTF.

### Documentation
Along with all the challenge files any documentation that is needed for the setup of your problem should be placed in the `README.md`.
For example any docker or compilation commands.  

### Solution
Along with the documentation in the `README.md` there should be a small writeup that includes the intended steps towards the solution.
The solution should be more than a one liner and include what security problem you are focusing on. Any scripts that help with the solution should be included as well. These solutions will be used by fellow devs during TAMUctf to help answer challengers questions so please be detailed where necessary.

### Reviewing
- When a PR has been submitted a fellow developer can review and possibly merge the PR
- When reviewing a problem consider the following:
  - Does the problem run correctly when set up using the guidelines posted?
  - Try to solve the problem on your own. How easy/hard is it for you? Is it apparent what you are supposed to do? If you get stuck try and follow the writeup. 
  - Are there any obvious bugs or simpler than expected solutions to the problems?
  - Is it fun?
- If there are problems that you have with the PR leave a comment explaining the problem and possibly a solution

To Review a PR:
1. Navigate to the PR
2. Click on `Files changed`
3. Click on the appropriate reponse (Approve, Comment, Etc)
4. If you approve the PR click the `Merge pull request` button to merge the PR
5. If you did not approve the PR make sure to leave any comments explaining why. 
    * Tip: You can leave line specific comments by clicking on the `+` next to the line number while under the `Files changed` tab

