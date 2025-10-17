package main

import (
    "context"
    "log"
    "net"

    "google.golang.org/grpc"
)

type parserServer struct{
}

func main() {
    lis, err := net.Listen("tcp", ":50051")
    if err != nil {
        log.Fatalf("failed to listen: %v", err)
    }

    grpcServer := grpc.NewServer()
    log.Println("DICOM parser gRPC service listening on :50051")

    if err := grpcServer.Serve(lis); err != nil {
        log.Fatalf("failed to serve: %v", err)
    }
}

