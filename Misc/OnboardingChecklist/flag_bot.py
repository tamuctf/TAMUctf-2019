import httplib2
import os
import re
import oauth2client
from oauth2client import client, tools, file
import base64
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText
from apiclient import errors, discovery

SCOPES = 'https://mail.google.com/'
CLIENT_SECRET_FILE = 'credentials.json'
APPLICATION_NAME = 'Gmail API Python Send Email'

def ListMessagesMatchingQuery(service, user_id, query=''):
    try:
        response = service.users().messages().list(userId=user_id,q=query).execute()
        messages = []
        if 'messages' in response:
            messages.extend(response['messages'])
        
        while 'nextPageToken' in response:
            page_token = response['nextPageToken']
            response = service.users().messages().list(userId=user_id, q=query,pageToken=page_token).execute()
            messages.extend(response['messages'])
        return messages
    except errors.HttpError as error:
        print('An error occurred: %s' % error)

def GetMessage(service, user_id, msg_id):
    try:
        message = service.users().messages().get(userId=user_id, id=msg_id).execute()
        return message
    except errors.HttpError as error:
        print('An error occurred: %s' % error)

def get_credentials():
    home_dir = os.path.expanduser('~')
    credential_dir = os.path.join(home_dir, '.credentials')
    if not os.path.exists(credential_dir):
        os.makedirs(credential_dir)
    credential_path = os.path.join(credential_dir, 'gmail-python-email-send.json')
    store = oauth2client.file.Storage(credential_path)
    credentials = store.get()
    if not credentials or credentials.invalid:
        flow = client.flow_from_clientsecrets(CLIENT_SECRET_FILE, SCOPES)
        flow.user_agent = APPLICATION_NAME
        credentials = tools.run_flow(flow, store)
        print('Storing credentials to ' + credential_path)
    return credentials  

def SendMessage(service, user_id, message):
    try:
        message = (service.users().messages().send(userId=user_id, body=message).execute())
        print('Message Id: %s' % message['id'])
        return message
    except errors.HttpError as error:
        print('An error occurred: %s' % error)

def CreateMessage(sender, to, subject, msgPlain):
    msg = MIMEMultipart('alternative')
    msg['Subject'] = subject
    msg['From'] = sender
    msg['To'] = to
    msg.attach(MIMEText(msgPlain, 'plain'))
    raw = base64.urlsafe_b64encode(msg.as_bytes())
    raw = raw.decode()
    body = {'raw': raw}
    return body

def findEmail(message):
    match = re.search(r'[\w\.-]+@[\w\.-]+', message)
    if match:
        return match.group(0)
    else:
        return 'No match'
    
def DeleteMessage(service, user_id, msg_id):
    try:
        service.users().messages().delete(userId=user_id, id=msg_id).execute()
        print('Message with id: %s deleted successfully.' % msg_id)
    except errors.HttpError as error:
        print('An error occurred: %s' % error)
   
def main():
    credentials = get_credentials()
    http = credentials.authorize(httplib2.Http())
    service = discovery.build('gmail', 'v1', http=http)
    inbox_ids = ListMessagesMatchingQuery(service,"me",'in:inbox')
    spam_ids = ListMessagesMatchingQuery(service,"me",'in:spam')
    message_ids = inbox_ids + spam_ids
    for message_id in message_ids:
        message = GetMessage(service, "me", message_id['id'])
        from_address = [value['value'] for value in message['payload']['headers'] if value['name'] == 'From'][0]
        print('From:',from_address)
        snippet = message['snippet']
        print('Body:',snippet)
        send_flag_address = findEmail(snippet)
        if '<someguy@somebigcorp.com>' in from_address and send_flag_address != 'No match':
            to = send_flag_address
            sender = "tamuctf@gmail.com"
            subject = "TAMUctf Flag"
            msgPlain = "gigem{wuT_4n_31337_sp0ofer_494C4F5645594F55}"
            reply_message = CreateMessage(sender, to, subject, msgPlain)
            SendMessage(service, "me", reply_message)
            print('sent flag to %s' % send_flag_address)
        DeleteMessage(service,"me",message_id['id'])

if __name__ == '__main__':
    main()