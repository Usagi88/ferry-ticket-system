importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');
   
firebase.initializeApp({
    apiKey: "AIzaSyBL5xZRLluTaCg94Aw9hfTI_bQ6HQSuUS8",
    projectId: "ticket-system-notification",
    messagingSenderId: "573089053325",
    appId: "1:573089053325:web:221a8f9dd5343a1b07c0c4"
});
  
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function({data:{title,body,icon}}) {
    return self.registration.showNotification(title,{body,icon});
});