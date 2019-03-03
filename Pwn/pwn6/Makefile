CC=gcc
CFLAGS= -l:libsqlite3.a -lcrypt -lpthread -ldl -O0
WFLAGS= -Wno-format-security
FFLAGS= -fno-stack-protector
DEPS_SERVER=src/server.c src/banking_handlers.c src/banking_handlers.h src/common.h src/fluff.c src/fluff.h src/md5.h src/md5.c
OBJS_SERVER=obj/server.o obj/banking_handlers.o obj/fluff.o obj/md5.o


DEPS_CLIENT=src/client.c src/common.h
OBJS_CLIENT=obj/client.o

all: server client

server: $(OBJS_SERVER)
	$(CC)  -o $@ $^ $(CFLAGS) $(WFLAGS) $(FFLAGS)

client: $(OBJS_CLIENT)
	$(CC) -o $@ $^ $(CFLAGS) $(WFLAGS) $(FFLAGS)

obj/%.o: src/%.c $(DEPS_CLIENT) $(DEPS_SERVER)
	$(CC) -c -o $@ $< $(CFLAGS) $(WFLAGS) $(FFLAGS)

clean:
	$(RM) obj/*.o server client
