import subprocess
from datetime import datetime

#imports
import time
from watchdog.observers import Observer
from watchdog.events import FileSystemEventHandler


#restart server

def restartServer():
    try:
        command = f'docker compose exec phpswoole bash ./project/restart.sh'
        result = subprocess.run(command, shell=True, capture_output=True, text=True)
        print(result.stdout)
    except Exception as e:
        print(f"Erro ao iniciar o servidor PHP: {str(e)}")
       #save log error (criar o arquivo caso n exista)
        with open('error_log.txt', 'a') as log_file:
            log_file.write(f"{datetime.now()}: {str(e)}\n")


#watcher
class Watcher:

    DIRECTORY_TO_WATCH_APP = "./app/src"
    DIRECTORY_TO_WATCH_LIB = "./libs"
    FILE_TO_WATCH_FUNCTIONS = "./server.php"

    def __init__(self):
        self.observer = Observer()
        #app
        self.observer.schedule(Handler(), self.DIRECTORY_TO_WATCH_APP, recursive=True)
        #libs
        self.observer.schedule(Handler(), self.DIRECTORY_TO_WATCH_LIB, recursive=True)
        #server
        self.observer.schedule(Handler(), self.FILE_TO_WATCH_FUNCTIONS, recursive=True)
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
            restartServer()
        elif event.event_type == 'modified':
            print("\033c", end="")
            # Taken any action here when a file is modified.
            print ("Received modified event - %s." % event.src_path)
            restartServer()
if __name__ == '__main__':
    w = Watcher()
    w.run()