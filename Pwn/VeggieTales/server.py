#!/usr/bin/env python3
import base64, string, pickle, codecs

my_episodes = []
all_episodes = ["1.  Wheres God When Im S-Scared?","2.  God Wants Me to Forgive Them!?!","3.  Are You My Neighbor?","4.  Rack, Shack and Benny","5.  Dave and the Giant Pickle","6.  The Toy That Saved Christmas","7.  Larry-Boy! And the Fib from Outer Space!","8.  Josh and the Big Wall!","9.  Madame Blueberry","10. Larry-Boy and the Rumor Weed","11. King George and the Ducky","12. Esther... The Girl Who Became Queen","13. Lyle the Kindly Viking","14. The Star of Christmas","15. The Wonderful World of Autotainment","16. The Ballad of Little Joe","17. An Easter Carol","18. A Snoodles Tale","19. Sumo of the Opera","20. Duke and the Great Pie War","21. Minnesota Cuke and the Search for Samsons Hairbrush","22. Lord of the Beans","23. Sheerluck Holmes and the Golden Ruler","24. LarryBoy and the Bad Apple","25. Gideon: Tuba Warrior","26. Moe and the Big Exit","27. The Wonderful Wizard of Has","28. Tomato Sawyer and Huckleberry Larrys Big River Rescue","29. Abe and the Amazing Promise","30. Minnesota Cuke and the Search for Noahs Umbrella","31. Saint Nicholas: A Story of Joyful Giving","32. Pistachio - The Little Boy That Woodnt","33. Sweetpea Beauty: A Girl After Gods Own Heart","34. Its a Meaningful Life","35. Twas The Night Before Easter","36. Princess and the Popstar","37. The Little Drummer Boy","38. Robin Good And His Not-So Merry Men","39. The Penniless Princess","40. The League of Incredible Vegetables","41. The Little House That Stood","42. MacLarry and the Stinky Cheese Battle","43. Merry Larry and the True Light of Christmas","44. Veggies in Space: The Fennel Frontier","45. Celery Night Fever","46. Beauty and the Beet","47. Noahs Ark"] 

def sortByNum(episode):
    return int(episode[:episode.find('.')])

def add_episode():
    for episode in all_episodes:
        print("%s" % episode)
    episode_num = str(input("Enter an episode (by number) to add to your watched list: "))
    while not (episode_num.isdigit() and (0 < int(episode_num) < 48)):
        episode_num = str(input("Enter a valid integer between 1 and 47!!"))
    if all_episodes[int(episode_num)-1] in my_episodes:
        print("That episode is already in your list.")
    else:
        my_episodes.append(all_episodes[int(episode_num)-1])
        print("episode added!")
    my_episodes.sort(key=sortByNum)

def check_list():
    print("----------------------")
    print("List of watched episodes:")
    if len(my_episodes) == 0:
        print(":(")
    for episode in my_episodes:
        print("%s" % episode)
    print("----------------------")


def backup_list():
    pickled = codecs.encode(str(base64.b64encode(pickle.dumps(my_episodes))),"rot-13").strip("o\'")
    print("Episode list backup string (Don't lose it!): %s\n" % pickled)

def load_list():
    answer = str(input("Load your backed up list here: "))
    try:
        global my_episodes
        my_episodes = pickle.loads(base64.b64decode(codecs.encode(answer,"rot-13")))
        print("Loaded backup\n")
    except:
        print("Invalid backup")

if __name__ == "__main__":
    print("Do you like VeggieTales??")
    message = "1. Add an episode to your watched list\n2. Print your watch list\n3. Backup your watch list\n4. Load your watch list\n"
    while True:
        listen = str(input(message))
        if len(listen) == 1 and listen in "1234":
            [add_episode, check_list, backup_list, load_list][int(listen) - 1]()
            message = "1. Add an episode to your watched list\n2. Print your watch list\n3. Backup your watch list\n4. Load your watch list\n"
        else:
            message = "Error: Please choose from options 1-4!!\n"
