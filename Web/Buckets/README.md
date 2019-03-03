# AWS Misconfiguration!

Setup files in an AWS S3 bucket as they are (folders and all) in Github. 
S3 bucket permissions as read, write, & access for all. To test: `http://ctfdevbucket.s3-website.us-east-2.amazonaws.com/`

### Setting up a S3 bucket to host a website
(Screen Shot 2018-10-18 at 11.21.59 AM.png)

### Setting up permissions (per file, index.html file as an example)
(Screen Shot 2018-10-18 at 11.22.55 AM.png)

## Solution
1. Make an AWS account
2. Setup AWS CLI access
    - https://docs.aws.amazon.com/polly/latest/dg/setup-aws-cli.html
    - https://docs.aws.amazon.com/cli/latest/userguide/cli-chap-install.html
    - https://docs.aws.amazon.com/cli/latest/userguide/cli-chap-configure.html
3. Give yourself permissions to read public s3 buckets
4. List the bucket contents with `aws s3 ls s3://tamuctf/`
5. Find the flag in  
`aws s3 ls s3://tamuctf/Dogs/CC2B70BD238F48BE29D8F0D42B170127/CBD2DD691D3DB1EBF96B283BDC8FD9A1/flag.txt`
6. Get the flag in the web browser or run  
`aws s3 cp s3://tamuctf/Dogs/CC2B70BD238F48BE29D8F0D42B170127/CBD2DD691D3DB1EBF96B283BDC8FD9A1/flag.txt .`
