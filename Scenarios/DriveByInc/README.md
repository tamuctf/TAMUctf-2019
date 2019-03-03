# Drive By Inc

1. Find `adminlogin.php` with `dirb`
2. Run `sqlmap` on `adminlogin.php` and extract out DB with usernames and passwords
3. Crack password for `devtest` user
4. User password to login as `devtest` user on the web server
5. Escalate privileges to the point that it can modify web files
6. Add a cryptojacking line to `index.html`
7. Add a user named `devtest2` with full privileges
8. Add a script to crontab that checks to make sure cryptominer and `devtest2` is still there and re-adds them if not

## Resources:
1. https://blogs.forcepoint.com/security-labs/browser-mining-coinhive-and-webassembly
