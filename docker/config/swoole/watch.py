import subprocess
from datetime import datetime

#imports
import os
import logging
import threading
import time
from watchdog.observers import Observer
from watchdog.events import FileSystemEventHandler


#restart server
def restartServer():
    try:
        command = f'./restart.sh &'
        result = subprocess.run(command, shell=True, capture_output=True, text=True)
        print(result.stdout)
    except Exception as e:
        print(f"Erro ao iniciar o servidor PHP: {str(e)}")


#watcher
class Watcher:

    DIRECTORY_TO_WATCH_LIBS = "../../../libs"
    DIRECTORY_TO_WATCH_APP= "../../../app"
    FILE_WATCH_SERVER= "../../../server.php"

    def __init__(self):
        self.observer = Observer()
        #libs
        self.observer.schedule(Handler(), self.DIRECTORY_TO_WATCH_LIBS, recursive=True)
        #app
        self.observer.schedule(Handler(), self.DIRECTORY_TO_WATCH_APP, recursive=True)
        #file
        self.observer.schedule(Handler(), self.FILE_WATCH_SERVER, recursive=True)
    def run(self):
        #site
        self.observer.start()
        try:
            while True:
                time.sleep(5)
        except:
            self.observer.stop()
            print ("Error")

        self.observer.join()

#event handler
class Handler(FileSystemEventHandler):

    @staticmethod
    def on_any_event(event):
        if event.is_directory:
            return None

        elif event.event_type == 'created':
            print("\033c", end="")
            # Take any action here when a file is first created.
            print ("Received created event - %s." % event.src_path)
            # ignore Tests
            if "Tests" in event.src_path:
                return None
            restartServer()
        elif event.event_type == 'modified':
            print("\033c", end="")
            # Taken any action here when a file is modified.
            print ("Received modified event - %s." % event.src_path)
            # ignore Tests
            if "Tests" in event.src_path:
                return None
            restartServer()
if __name__ == '__main__':
    w = Watcher()
    w.run()
