package main

import (
	"fmt"
	"math/rand"
	"os"
	"regexp"
	"syscall"
	"time"
)

const (
	PipePath    = "/var/run/stats"
	Interval    = 100 * time.Millisecond
	Mu          = 18.0
	Sigma       = 1.5
	FailureRate = 0.025

	NoteToReverseEngineers = `Are you reverse engineering this right now?  I
appreciate your dedication, but the flag ain't here... just some mediocore Go
code that writes fake mining stats to a FIFO. I probably shouldn't have even
bothered writing it, but the fact that you are reading this write now makes me
feel that it was worth it.`
)

var (
	birth = time.Now()
)

func Stats(wallet string) []byte {
	uptime := time.Since(birth).Seconds()
	rate := rand.NormFloat64()*Sigma + Mu
	return []byte(fmt.Sprintf("dst %s\nuptime %.f s\n%.2f Mhash/s\n0 blocks mined\n", wallet, uptime, rate))
}

func WriteStats(path, wallet string) error {
	pipe, err := os.OpenFile(path, os.O_WRONLY, 0600)
	if err != nil {
		return fmt.Errorf("failed to open pipe for write: %v", err)
	}
	defer pipe.Close()
	if rand.Float64() < FailureRate {
		return fmt.Errorf("miner has died: SIGSEGV")
	} else if _, err := pipe.Write(Stats(wallet)); err != nil {
		return fmt.Errorf("failed to write to pipe: %v", err)
	}
	return nil
}

func OpenPipe(path string) error {
	if stat, err := os.Stat(path); err != nil {
		if os.IsNotExist(err) {
			if err := syscall.Mkfifo(path, 0600); err != nil {
				return fmt.Errorf("failed to open pipe at %s: %v", path, err)
			}
			return nil
		}
		return fmt.Errorf("failed to stat pipe at %s: %v", path, err)
	} else if stat.Mode()&os.ModeNamedPipe == 0 {
		return fmt.Errorf("file at %s is not a pipe", path)
	}
	return nil
}

func main() {
	wallet := os.Args[1]
	match, err := regexp.MatchString(`^[13][a-km-zA-HJ-NP-Z1-9]{25,34}$`, wallet)
	if err != nil {
		fmt.Print(err)
		os.Exit(1)
	}
	if !match {
		fmt.Printf("%s is not a valid address", wallet)
		os.Exit(1)
	}

	if err := OpenPipe(PipePath); err != nil {
		fmt.Print(err)
		os.Exit(1)
	}
	for {
		if err := WriteStats(PipePath, wallet); err != nil {
			fmt.Print(err)
			os.Exit(1)
		}
		time.Sleep(Interval)
	}
}

func init() {
	rand.Seed(time.Now().UnixNano())
}
