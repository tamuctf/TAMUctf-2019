# Poisoned Docker Compose

Forensic scenario that centers around a developer mistakenly using a poisoned docker-compose file.

## Scenario

1. Developer downloads poisoned compose file that has poisoned docker images.  
2. Poisoned docker compose/image steals ssh keys and shadow file upon startup.  
3. Attacker uses keys to login into server.  
4. Attacker attaches to sql database container and dumps db.  
5. Attacker exfiltrates db that contains passwords and credit cards.  
6. Attacker uses container root filesystem access to escalate priviliges by adding a root user named dev.  
7. Attacker pulls down and runs custom kali docker image.  

[Disk Image](https://drive.google.com/a/tamu.edu/file/d/19zgsmqMZ_QltLYzWcCdxizV9Wipj-2NI/view?usp=sharing)

## Questions
1.  00_intrusion
    - What is the IP Address of the attacker?
      - 10.91.9.93
      
2. 01_logs
    - What user was the attacker able to login as?
      - root
    - What is the date & time that the attacker logged in? (MM/DD HH:MM:SS)
      - 02/17 00:06:04
    
3. 02_analysis
    - What is the name of the service that was used to compromise the machine?
      - docker
    - What is the md5sum of the initial compromising file?
      - a2111283f69aafcd658f558b0402fbc4
    - What specific line in the initial compromising file was the most dangerous?
      - "- /:/tmp"
    
4. 03_forensics
    - What are the last names of customers who got compromised? (alphabetical order, comma separated ex: `asdf, bsdf`)
      - Billy, Face, Frank, John, Meserole, Orange, Suzy
    - What is the md5sum of the file that was used to exfiltrate data initially?
      - 14b0d800ce6f2882a6f058b45fc500c8
    - What is the md5sum of the file that was stolen after the attacker logged in?
      - 6d47d74d66e96c9bce2720c8a56f2558
    
5. 04_persistence
    - What is the new user that was created?
      - dev
    - What is the full name of the new docker image that was pulled down?
      - tamuctf/kaliimage
