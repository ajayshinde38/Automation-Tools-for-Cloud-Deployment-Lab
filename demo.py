# Simple Chatbot Program in Python

print("Hello! I am ChatBot. Type 'bye' to exit.")

while True:
    user_input = input("You: ").lower()
    
    if user_input in ["hi", "hello", "hey"]:
        print("ChatBot: Hello! How are you?")
        
    elif user_input in ["i am fine", "fine", "good"]:
        print("ChatBot: Nice to hear that! What can I do for you?")
        
    elif user_input in ["who are you", "what are you"]:
        print("ChatBot: I am a simple chatbot created in Python.")
        
    elif user_input in ["your name", "what is your name"]:
        print("ChatBot: My name is ChatBot.")
        
    elif user_input in ["bye", "exit", "quit"]:
        print("ChatBot: Goodbye! Have a nice day.")
        break
        
    elif "weather" in user_input:
        print("ChatBot: I can't check weather yet, but I hope it's nice outside!")
        
    elif "time" in user_input:
        from datetime import datetime
        print("ChatBot: The current time is", datetime.now().strftime("%H:%M:%S"))
        
    else:
        print("ChatBot: Sorry, I didn’t understand that.")
